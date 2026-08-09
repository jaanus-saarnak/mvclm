<?php
/**
 * Issues a password reset link and mails it
 *
 * The one place the reset email is composed. Callers ask
 * PasswordResetModel::issuedRecently() before calling sendLink(); nothing here
 * throttles, and nothing here decides whether the account exists.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App;

use RuntimeException;
use App\Models\PasswordResetModel;
use Core\Mailer;

/**
 * Composes and sends the message carrying a password reset link.
 */
class PasswordReset
{
    /**
     * Whether this install offers to reset a forgotten password by email.
     *
     * Read through here rather than from CONFIG directly, so a missing key in
     * an env.php written before this feature existed means off rather than a
     * warning.
     *
     * @return bool True if the feature is switched on
     */
    public static function isEnabled(): bool
    {
        return (bool) (CONFIG['enable_password_reset'] ?? false);
    }

    /**
     * Issue a fresh link for an account and mail it to them.
     *
     * Issuing revokes whatever link the account was holding, so the message
     * that arrives last is always the one that works.
     *
     * @param int $uid The user's unique identifier
     * @param string $email Where to send it
     * @param string $username Who to greet
     * @throws RuntimeException If the token cannot be stored or the mail cannot be sent
     * @return void
     */
    public static function sendLink(int $uid, string $email, string $username): void
    {
        $token = PasswordResetModel::issue($uid);
        $link = CONFIG['url'] . '/reset-password/' . $token;

        Mailer::send($email, self::subject(), self::htmlBody($username, $link), self::textBody($username, $link));
    }

    /**
     * The subject line of the reset message.
     *
     * @return string The subject
     */
    private static function subject(): string
    {
        return 'Reset your ' . CONFIG['app_name'] . ' password';
    }

    /**
     * How long the link lasts, written the way a sentence needs it.
     *
     * Kept beside the message rather than in the model, because the model's
     * constant is a number and this is grammar. One hour is the shipped value
     * and "1 hours" would be wrong. Public because the views say it too, and a
     * rule written in two places is a rule that will disagree with itself.
     *
     * @return string A phrase such as 'an hour' or '24 hours'
     */
    public static function lifetimePhrase(): string
    {
        $hours = PasswordResetModel::LIFETIME_HOURS;

        return $hours === 1 ? 'an hour' : $hours . ' hours';
    }

    /**
     * The HTML body of the reset message.
     *
     * Written the way email is written rather than the way this application's
     * pages are: tables for layout, styles inline on every element, one fixed
     * width, and no external file of any kind. A mail client is not a browser,
     * a stylesheet in the head is discarded by several of them, and a remote
     * image is blocked by most until the reader allows it.
     *
     * @param string $username The account's username
     * @param string $link The full reset URL
     * @return string The message body
     */
    private static function htmlBody(string $username, string $link): string
    {
        $app = htmlspecialchars(CONFIG['app_name'], ENT_QUOTES);
        $name = htmlspecialchars($username, ENT_QUOTES);
        $url = htmlspecialchars($link, ENT_QUOTES);
        $window = self::lifetimePhrase();

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reset your password</title>
        </head>
        <body style="margin:0; padding:0; background-color:#f3f4f6; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

        <div style="display:none; max-height:0; max-width:0; overflow:hidden; opacity:0; font-size:0; line-height:0; mso-hide:all;">Choose a new password for your {$app} account.</div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;">
        <tr><td align="center" style="padding:32px 16px;">

        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:100%; background-color:#ffffff; border-radius:12px; border:1px solid #e5e7eb;">

        <tr><td style="padding:28px 32px 0 32px;">
        <div style="font-size:18px; font-weight:700; color:#4f46e5;">{$app}</div>
        </td></tr>

        <tr><td style="padding:24px 32px 0 32px;">
        <h1 style="margin:0; font-size:22px; line-height:30px; font-weight:600; color:#111827;">Reset your password</h1>
        <p style="margin:16px 0 0 0; font-size:15px; line-height:24px; color:#4b5563;">
        Hello {$name}, someone asked to reset the password on your {$app} account. Choose a new one here.
        </p>
        </td></tr>

        <tr><td align="center" style="padding:28px 32px 0 32px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr><td align="center" bgcolor="#4f46e5" style="border-radius:8px; padding:13px 28px;">
        <a href="{$url}" style="display:inline-block; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">Choose a new password</a>
        </td></tr>
        </table>
        </td></tr>

        <tr><td style="padding:24px 32px 0 32px;">
        <p style="margin:0; font-size:13px; line-height:20px; color:#6b7280;">
        If the button does not work, paste this into your browser:
        </p>
        <p style="margin:6px 0 0 0; font-size:13px; line-height:20px; color:#4f46e5; word-break:break-all;">{$url}</p>
        </td></tr>

        <tr><td style="padding:24px 32px 28px 32px;">
        <div style="border-top:1px solid #e5e7eb; padding-top:16px;">
        <p style="margin:0; font-size:13px; line-height:20px; color:#6b7280;">
        The link works once and expires in {$window}. If it has expired, ask for another from the sign-in page.
        </p>
        <p style="margin:10px 0 0 0; font-size:13px; line-height:20px; color:#6b7280;">
        If you did not ask for this, ignore this message. Your password stays as it is until the link is used.
        </p>
        </div>
        </td></tr>

        </table>

        <p style="margin:20px 0 0 0; font-size:12px; line-height:18px; color:#9ca3af;">{$app}</p>

        </td></tr>
        </table>

        </body>
        </html>
        HTML;
    }

    /**
     * The plain text body of the reset message.
     *
     * @param string $username The account's username
     * @param string $link The full reset URL
     * @return string The message body
     */
    private static function textBody(string $username, string $link): string
    {
        $window = self::lifetimePhrase();

        return "Hello {$username},\n\n"
            . "Someone asked to reset the password on your " . CONFIG['app_name'] . " account.\n"
            . "Choose a new one here.\n\n"
            . "{$link}\n\n"
            . "The link works once and expires in {$window}. If it has, ask for another\n"
            . "from the sign-in page.\n\n"
            . "If you did not ask for this, ignore this message. Your password stays as\n"
            . "it is until the link is used.\n";
    }
}
