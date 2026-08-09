<?php
/**
 * Password reset tokens, stored in the password_resets table
 *
 * An account holds one outstanding token at a time, so asking for another link
 * revokes the one before it. Ask issuedRecently() before issue() rather than
 * after: issuing is what revokes, so the throttle cannot see a token that the
 * call it is guarding has already deleted.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Core\Model;

/**
 * Issues, finds and retires the tokens that let a forgotten password be replaced.
 */
class PasswordResetModel
{
    /**
     * Hours a token stays usable after it is issued.
     *
     * Shorter than the verification equivalent on purpose. A confirmation link
     * only proves an address was reached; this one replaces a password, so it
     * is worth less time in an inbox.
     */
    public const LIFETIME_HOURS = 1;

    /**
     * Minutes an account is left alone after a link is sent to it.
     */
    public const THROTTLE_MINUTES = 2;

    /**
     * Issue a token for a user and return it.
     *
     * The return value is the only copy of the token that will ever exist in
     * readable form. It goes into the email and is not recoverable afterwards,
     * which is the point of storing the hash.
     *
     * Any token the account already holds is revoked first, so a second request
     * cannot leave two working links in two different inboxes.
     *
     * @param int $uid The user's unique identifier
     * @throws RuntimeException If the token cannot be stored
     * @return string The token, 64 hex characters
     */
    public static function issue(int $uid): string
    {
        $token = bin2hex(random_bytes(32));

        self::revokeFor($uid);
        self::purgeExpired();

        try {
            // The interval is a constant rather than anything a caller supplies,
            // and the expiry is computed by the database so it shares a clock
            // with the created column beside it.
            Model::sqlQueryPrepared(
                "INSERT INTO `password_resets` (`uid`, `token_hash`, `expires`)
                 VALUES (?, ?, NOW() + INTERVAL " . (int) self::LIFETIME_HOURS . " HOUR)",
                [(string)$uid, self::hash($token)]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to issue a password reset token: " . $e->getMessage(), 0, $e);
        }

        return $token;
    }

    /**
     * Whether this account was sent a link recently enough to be left alone.
     *
     * This is the whole rate limit. It is keyed on the account rather than on
     * the requester because the requester cannot be identified: userIp() reads
     * client-settable headers, so anything counted per address is bypassed by
     * sending one more header. What it protects is the inbox of the person
     * being named, which is the thing an attacker can otherwise flood.
     *
     * @param int $uid The user's unique identifier
     * @throws RuntimeException If the query fails
     * @return bool True if a live token was issued inside the throttle window
     */
    public static function issuedRecently(int $uid): bool
    {
        try {
            $row = Model::getSingleRowPrepared(
                "SELECT COUNT(*) AS `recent` FROM `password_resets`
                 WHERE `uid` = ?
                   AND `used` IS NULL
                   AND `expires` > NOW()
                   AND `created` > NOW() - INTERVAL " . (int) self::THROTTLE_MINUTES . " MINUTE",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to check for a recent password reset token: " . $e->getMessage(), 0, $e);
        }

        return $row !== false && (int) $row['recent'] > 0;
    }

    /**
     * Find the row a token belongs to, whatever state it is in.
     *
     * Deliberately does not filter out spent or expired rows. Telling someone
     * their link has expired is a different page from telling them it was never
     * a link at all, and the caller cannot make that distinction from a null.
     *
     * @param string $token The token as it arrived from the URL
     * @throws RuntimeException If the query fails
     * @return array|null The row, or null if no token matches
     */
    public static function findByToken(string $token): ?array
    {
        try {
            $row = Model::getSingleRowPrepared(
                "SELECT * FROM `password_resets` WHERE `token_hash` = ? LIMIT 1",
                [self::hash($token)]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to look up a password reset token: " . $e->getMessage(), 0, $e);
        }

        return $row !== false ? $row : null;
    }

    /**
     * Whether a row found by findByToken() can still be redeemed.
     *
     * @param array $row A row from findByToken()
     * @return bool True only if the token is unspent and inside its lifetime
     */
    public static function isUsable(array $row): bool
    {
        if (isset($row['used'])) {
            return false;
        }

        return isset($row['expires']) && strtotime((string) $row['expires']) > time();
    }

    /**
     * Mark a token as spent.
     *
     * Spent rather than deleted, so following a link twice can be answered with
     * "this was already used" instead of "no such link".
     *
     * @param int $id The row's identifier
     * @throws RuntimeException If the update fails
     * @return void
     */
    public static function markUsed(int $id): void
    {
        try {
            Model::sqlQueryPrepared(
                "UPDATE `password_resets` SET `used` = NOW() WHERE `id` = ?",
                [(string)$id]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to mark a password reset token as used: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Revoke every outstanding token an account holds.
     *
     * Called when a password changes as well as when a link is issued. Whoever
     * has just set the password is the person the outstanding links were for,
     * and leaving one alive would let an old message undo the new password.
     *
     * @param int $uid The user's unique identifier
     * @throws RuntimeException If the deletion fails
     * @return void
     */
    public static function revokeFor(int $uid): void
    {
        try {
            Model::sqlQueryPrepared(
                "DELETE FROM `password_resets` WHERE `uid` = ? AND `used` IS NULL",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to revoke password reset tokens: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Drop rows nobody can act on any more.
     *
     * Called from issue() rather than from a scheduled task, because this
     * framework has nowhere to schedule one and the table would otherwise grow
     * without limit. Spent rows are kept a week so a second click still gets
     * the honest answer.
     *
     * @throws RuntimeException If the deletion fails
     * @return void
     */
    public static function purgeExpired(): void
    {
        try {
            Model::sqlQuery(
                "DELETE FROM `password_resets`
                 WHERE `expires` < NOW() - INTERVAL 7 DAY
                    OR `used` < NOW() - INTERVAL 7 DAY"
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to purge spent password reset tokens: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Hash a token the way the table stores it.
     *
     * sha256 rather than password_hash, because a lookup has to find the row
     * from the token alone and a salted hash cannot be searched for. The token
     * is 256 bits from random_bytes(), so it has no guessable structure for the
     * plain hash to leak.
     *
     * @param string $token The token in readable form
     * @return string The hash, 64 hex characters
     */
    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
