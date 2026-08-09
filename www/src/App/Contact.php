<?php
/**
 * Composes and sends a contact form message
 *
 * The address it sends to is contact_to_address in env.php. Leave that empty
 * and isAvailable() answers false, which is how an install without it switches
 * the form off rather than offering one with nowhere to send.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App;

use RuntimeException;
use Core\Mailer;

/**
 * Turns three fields from a visitor into one message to the site's owner.
 */
class Contact
{
    /**
     * Shortest and longest name the form accepts.
     */
    public const NAME_MIN = 2;
    public const NAME_MAX = 60;

    /**
     * Shortest and longest message the form accepts.
     *
     * The upper bound is what keeps a single submission from being used to send
     * an unbounded amount of text to the owner's mailbox.
     */
    public const MESSAGE_MIN = 10;
    public const MESSAGE_MAX = 3000;

    /**
     * Whether this install has somewhere to send a contact message.
     *
     * Read through here rather than from CONFIG directly, so an env.php written
     * before this feature existed means off rather than a warning.
     *
     * @return bool True if a recipient address is configured
     */
    public static function isAvailable(): bool
    {
        return self::recipient() !== '';
    }

    /**
     * Send one message to the site's owner.
     *
     * The visitor's address becomes the Reply-To rather than the From, because
     * mail sent from an address this server cannot authenticate for is what SPF
     * and DMARC exist to reject. Replying still reaches them.
     *
     * @param string $name The visitor's name, already validated
     * @param string $email The visitor's address, already validated
     * @param string $message What they wrote, already validated
     * @throws RuntimeException If the message cannot be sent
     * @return void
     */
    public static function send(string $name, string $email, string $message): void
    {
        Mailer::send(
            self::recipient(),
            self::subject($name),
            '',
            self::textBody($name, $email, $message),
            $email
        );
    }

    /**
     * The address contact messages go to.
     *
     * @return string The configured address, or an empty string if there is none
     */
    private static function recipient(): string
    {
        return trim((string) (CONFIG['contact_to_address'] ?? ''));
    }

    /**
     * The subject line.
     *
     * Composed here rather than taken from the visitor, so the one line that
     * lands in the owner's inbox list cannot be written by whoever submitted the
     * form. The name is still theirs, which is why it comes after a fixed prefix
     * and is bounded to NAME_MAX: a message can carry an unwanted phrase, but it
     * cannot disguise itself as something other than a contact form.
     *
     * @param string $name The visitor's name
     * @return string The subject
     */
    private static function subject(string $name): string
    {
        return 'Contact form: ' . $name;
    }

    /**
     * The message body.
     *
     * Plain text only, unlike the two mails this framework sends to visitors.
     * Those are read by the person who signed up and are worth styling; this one
     * is read by the site's owner, and text keeps the visitor's own line breaks
     * without an escaping step between what they typed and what arrives.
     *
     * @param string $name The visitor's name
     * @param string $email The visitor's address
     * @param string $message What they wrote
     * @return string The message body
     */
    private static function textBody(string $name, string $email, string $message): string
    {
        return "Name:  {$name}\n"
            . "Email: {$email}\n\n"
            . "{$message}\n\n"
            . "-- \n"
            . "Sent from the contact form at " . CONFIG['url'] . "/contact\n"
            . "Reply to this message to answer them.\n";
    }
}
