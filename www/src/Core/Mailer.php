<?php
/**
 * Sends mail over authenticated SMTP
 *
 * Configured entirely from the smtp_ and mail_from_ keys in env.php. Call
 * Mailer::send() and catch RuntimeException, which is what every failure
 * arrives as.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

use RuntimeException;

/**
 * A minimal SMTP client covering submission to an authenticating relay.
 *
 * Written rather than pulled in because the two obvious libraries both cost
 * more than they save here. PHPMailer is LGPL-2.1 and this framework bundles
 * only MIT code; symfony/mailer is MIT but carries six dependencies into a
 * composer.json that requires no packages at all.
 *
 * The scope that makes that trade honest: one recipient, no attachments, no
 * DKIM. A message needing any of those wants a real library instead.
 */
class Mailer
{
    /**
     * Seconds to wait on the connection and on every read after it.
     */
    private const TIMEOUT = 15;

    /**
     * SMTP line terminator. Required by the protocol, and not the same as PHP_EOL.
     */
    private const CRLF = "\r\n";

    /**
     * The open connection, or null before connect() and after close().
     *
     * @var resource|null
     */
    private $socket = null;

    /**
     * Send one message.
     *
     * The only entry point. Both bodies are optional individually but at least
     * one has to be present; supplying both sends multipart/alternative, which
     * is what a mail client wants so it can pick.
     *
     * @param string $to Recipient address
     * @param string $subject Subject line, any UTF-8
     * @param string $htmlBody The HTML part, or an empty string for none
     * @param string $textBody The plain text part, or an empty string for none
     * @param string $replyTo Address a reply should go to, or an empty string for none
     * @throws RuntimeException On bad arguments, missing settings, or any refusal from the server
     * @return void
     */
    public static function send(string $to, string $subject, string $htmlBody = '', string $textBody = '', string $replyTo = ''): void
    {
        if ($htmlBody === '' && $textBody === '') {
            throw new RuntimeException('Refusing to send a message with no body.');
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Refusing to send to an address that is not valid.');
        }

        // Refusing rather than dropping it. A caller passing a reply address
        // wants replies to reach it, so silently sending without one would hide
        // the failure until somebody answered a message and nothing arrived.
        if ($replyTo !== '' && !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Refusing to use a reply address that is not valid.');
        }

        $mailer = new self();

        try {
            $mailer->connect();
            $mailer->authenticate();
            $mailer->deliver($to, $subject, $htmlBody, $textBody, $replyTo);
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Core\Error turns PHP warnings into exceptions, including ones
            // written with @, so a socket call can fail as an ErrorException
            // before its own false return is ever inspected. Converting here is
            // what makes "every failure is a RuntimeException" true for callers
            // rather than only true on the command line.
            throw new RuntimeException('Could not send mail: ' . $e->getMessage(), 0, $e);
        } finally {
            // Runs on the failure path too, so a refused message still releases
            // the socket rather than leaving the relay holding a session open.
            $mailer->close();
        }
    }

    /**
     * Open the connection and get as far as an encrypted, greeted session.
     *
     * smtp_encryption picks between the two ways a relay offers TLS. 'ssl'
     * wraps the socket from the first byte, which is port 465. 'tls' connects
     * in the clear and upgrades with STARTTLS, which is 587 and Mailtrap's
     * 2525. An empty value sends credentials over plaintext and exists only
     * for a relay on the same host.
     *
     * @throws RuntimeException If a setting is missing or the server will not talk
     * @return void
     */
    private function connect(): void
    {
        $host = self::setting('smtp_host');
        $port = (int) self::setting('smtp_port');
        $encryption = strtolower(self::setting('smtp_encryption', false));

        $address = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;

        $errorNumber = 0;
        $errorMessage = '';
        $socket = @stream_socket_client($address, $errorNumber, $errorMessage, self::TIMEOUT);

        if ($socket === false) {
            throw new RuntimeException("Could not connect to {$address}: {$errorMessage} ({$errorNumber})");
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, self::TIMEOUT);

        $this->expect(220, 'greeting');
        $this->hello();

        if ($encryption === 'tls') {
            $this->command('STARTTLS');
            $this->expect(220, 'STARTTLS');

            if (@stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT) !== true) {
                throw new RuntimeException('STARTTLS was accepted but the TLS handshake failed.');
            }

            // The capability list from before the upgrade cannot be trusted, so
            // the protocol requires greeting again on the encrypted channel.
            $this->hello();
        }
    }

    /**
     * Greet the server with EHLO.
     *
     * @throws RuntimeException If the server rejects the greeting
     * @return void
     */
    private function hello(): void
    {
        $url = defined('CONFIG') && isset(CONFIG['url']) ? (string) CONFIG['url'] : '';
        $clientName = parse_url($url, PHP_URL_HOST) ?: 'localhost';

        $this->command('EHLO ' . $clientName);
        $this->expect(250, 'EHLO');
    }

    /**
     * Authenticate with AUTH LOGIN.
     *
     * Chosen over AUTH PLAIN because every relay worth using accepts it and it
     * keeps the two secrets in separate exchanges. Neither is protection on its
     * own; the encryption from connect() is what protects them.
     *
     * @throws RuntimeException If the credentials are missing or refused
     * @return void
     */
    private function authenticate(): void
    {
        $username = self::setting('smtp_username');
        $password = self::setting('smtp_password');

        $this->command('AUTH LOGIN');
        $this->expect(334, 'AUTH LOGIN');

        $this->command(base64_encode($username));
        $this->expect(334, 'SMTP username');

        $this->command(base64_encode($password));

        // Deliberately does not repeat what was sent. A failure here is the one
        // place a thrown message could otherwise carry the password into a log.
        $this->expect(235, 'SMTP password');
    }

    /**
     * Run the envelope and hand over the message itself.
     *
     * @param string $to Recipient address, already validated
     * @param string $subject Subject line
     * @param string $htmlBody The HTML part, or an empty string
     * @param string $textBody The plain text part, or an empty string
     * @param string $replyTo Reply address, already validated, or an empty string
     * @throws RuntimeException If the server rejects the envelope or the message
     * @return void
     */
    private function deliver(string $to, string $subject, string $htmlBody, string $textBody, string $replyTo): void
    {
        $fromAddress = self::setting('mail_from_address');

        if (!filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('mail_from_address in env.php is not a valid address.');
        }

        $this->command('MAIL FROM:<' . $fromAddress . '>');
        $this->expect(250, 'MAIL FROM');

        $this->command('RCPT TO:<' . $to . '>');
        $this->expect(250, 'RCPT TO');

        $this->command('DATA');
        $this->expect(354, 'DATA');

        $this->command($this->message($to, $subject, $htmlBody, $textBody, $replyTo) . self::CRLF . '.');
        $this->expect(250, 'the message body');

        $this->command('QUIT');
    }

    /**
     * Build the headers and body of the message.
     *
     * Every body part is base64 encoded rather than sent as-is. That is not
     * about secrecy: it removes the two ways a raw body breaks SMTP, which are
     * lines past the 998-character limit and a line consisting of a single dot.
     *
     * @param string $to Recipient address
     * @param string $subject Subject line
     * @param string $htmlBody The HTML part, or an empty string
     * @param string $textBody The plain text part, or an empty string
     * @param string $replyTo Reply address, already validated, or an empty string
     * @return string The complete message, CRLF terminated throughout
     */
    private function message(string $to, string $subject, string $htmlBody, string $textBody, string $replyTo): string
    {
        $fromAddress = self::setting('mail_from_address');
        $fromName = self::setting('mail_from_name', false);
        $domain = substr(strrchr($fromAddress, '@') ?: '@localhost', 1);

        $from = $fromName === ''
            ? $fromAddress
            : '"' . self::encodeHeader($fromName) . '" <' . $fromAddress . '>';

        $headers = [
            'Date: ' . date('r'),
            'From: ' . $from,
        ];

        // Only present when a caller asked for it, so the two mail features that
        // predate this parameter produce exactly the headers they always did.
        if ($replyTo !== '') {
            $headers[] = 'Reply-To: ' . $replyTo;
        }

        $headers[] = 'To: ' . $to;
        $headers[] = 'Subject: ' . self::encodeHeader($subject);
        $headers[] = 'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>';
        $headers[] = 'MIME-Version: 1.0';

        if ($htmlBody !== '' && $textBody !== '') {
            $boundary = bin2hex(random_bytes(16));
            $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

            // Plain text goes first. In multipart/alternative the last part a
            // client understands is the one it shows, so this order is what
            // makes the HTML win where there is a choice.
            $body = '--' . $boundary . self::CRLF
                . self::part('text/plain', $textBody)
                . '--' . $boundary . self::CRLF
                . self::part('text/html', $htmlBody)
                . '--' . $boundary . '--';
        } else {
            $type = $htmlBody !== '' ? 'text/html' : 'text/plain';
            $headers[] = 'Content-Type: ' . $type . '; charset=UTF-8';
            $headers[] = 'Content-Transfer-Encoding: base64';
            $body = trim(chunk_split(base64_encode($htmlBody !== '' ? $htmlBody : $textBody), 76, self::CRLF));
        }

        $message = implode(self::CRLF, $headers) . self::CRLF . self::CRLF . $body;

        // Doubling a leading dot is what stops a body line ending the DATA
        // block early. Base64 output cannot produce one, so this guards the
        // encoding changing rather than anything sent today.
        return preg_replace('/^\./m', '..', $message) ?? $message;
    }

    /**
     * Render one part of a multipart message.
     *
     * @param string $type The MIME type of this part
     * @param string $content The part's content, unencoded
     * @return string The part with its own headers, CRLF terminated
     */
    private static function part(string $type, string $content): string
    {
        return 'Content-Type: ' . $type . '; charset=UTF-8' . self::CRLF
            . 'Content-Transfer-Encoding: base64' . self::CRLF
            . self::CRLF
            . trim(chunk_split(base64_encode($content), 76, self::CRLF)) . self::CRLF;
    }

    /**
     * Make a value safe to place in a header.
     *
     * Strips the line breaks that would otherwise let a subject inject headers
     * of its own, then base64 encodes anything outside printable ASCII so a
     * non-English subject survives. Long non-ASCII values are not folded, which
     * is fine for a subject line and would not be for a long address list.
     *
     * @param string $value The raw header value
     * @return string The value, safe to concatenate into a header line
     */
    private static function encodeHeader(string $value): string
    {
        $value = str_replace(["\r", "\n", '"'], '', $value);

        if (preg_match('/^[\x20-\x7E]*$/', $value) === 1) {
            return $value;
        }

        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    /**
     * Read one setting out of CONFIG.
     *
     * @param string $key The CONFIG key
     * @param bool $required Whether an empty value is an error
     * @throws RuntimeException If a required setting is absent or empty
     * @return string The setting's value, cast to string
     */
    private static function setting(string $key, bool $required = true): string
    {
        $value = defined('CONFIG') && isset(CONFIG[$key]) ? trim((string) CONFIG[$key]) : '';

        if ($required && $value === '') {
            throw new RuntimeException("Cannot send mail: '{$key}' is not set in env.php.");
        }

        return $value;
    }

    /**
     * Write one command to the server.
     *
     * @param string $line The line to send, without its terminator
     * @throws RuntimeException If the write fails
     * @return void
     */
    private function command(string $line): void
    {
        if ($this->socket === null || fwrite($this->socket, $line . self::CRLF) === false) {
            throw new RuntimeException('Lost the connection to the mail server while sending.');
        }
    }

    /**
     * Read one reply and require a particular status code.
     *
     * @param int $expected The status code that means success at this step
     * @param string $step What was being attempted, for the error message
     * @throws RuntimeException If the reply is missing, times out, or carries another code
     * @return void
     */
    private function expect(int $expected, string $step): void
    {
        $reply = $this->read();

        if ((int) substr($reply, 0, 3) !== $expected) {
            throw new RuntimeException("The mail server refused {$step}: " . trim($reply));
        }
    }

    /**
     * Read a complete reply, however many lines it runs to.
     *
     * A multi-line reply marks every line but its last with a hyphen in the
     * fourth column, so that is the terminator rather than the first newline.
     *
     * @throws RuntimeException If the connection closes or the read times out
     * @return string The reply, newline separated
     */
    private function read(): string
    {
        $lines = [];

        while (true) {
            $line = $this->socket === null ? false : fgets($this->socket, 1024);

            if ($line === false) {
                throw new RuntimeException('The mail server closed the connection or stopped answering.');
            }

            $lines[] = rtrim($line, "\r\n");

            if (stream_get_meta_data($this->socket)['timed_out']) {
                throw new RuntimeException('Timed out waiting for the mail server.');
            }

            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Close the connection if one is open.
     *
     * @return void
     */
    private function close(): void
    {
        if ($this->socket !== null) {
            @fclose($this->socket);
            $this->socket = null;
        }
    }
}
