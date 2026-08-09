<?php
/**
 * Email verification tokens, stored in the email_verifications table
 *
 * An account holds one outstanding token at a time, so asking for another link
 * revokes the one before it. issue() is the only place the token itself exists;
 * the table keeps a hash of it.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Core\Model;

/**
 * Issues, finds and retires the tokens that prove an address was reached.
 */
class EmailVerificationModel
{
    /**
     * Hours a token stays usable after it is issued.
     */
    public const LIFETIME_HOURS = 24;

    /**
     * Issue a token for a user and return it.
     *
     * The return value is the only copy of the token that will ever exist in
     * readable form. It goes into the email and is not recoverable afterwards,
     * which is the point of storing the hash.
     *
     * Any token the account already holds is revoked first, so a resend cannot
     * leave two working links in two different inboxes.
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
            Model::sqlQueryPrepared(
                "INSERT INTO `email_verifications` (`uid`, `token_hash`, `expires`)
                 VALUES (?, ?, NOW() + INTERVAL " . (int) self::LIFETIME_HOURS . " HOUR)",
                [(string)$uid, self::hash($token)]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to issue an email verification token: " . $e->getMessage(), 0, $e);
        }

        return $token;
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
                "SELECT * FROM `email_verifications` WHERE `token_hash` = ? LIMIT 1",
                [self::hash($token)]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to look up an email verification token: " . $e->getMessage(), 0, $e);
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
     * Spent rather than deleted, so redeeming a link twice can be answered with
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
                "UPDATE `email_verifications` SET `used` = NOW() WHERE `id` = ?",
                [(string)$id]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to mark an email verification token as used: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Revoke every outstanding token an account holds.
     *
     * @param int $uid The user's unique identifier
     * @throws RuntimeException If the deletion fails
     * @return void
     */
    public static function revokeFor(int $uid): void
    {
        try {
            Model::sqlQueryPrepared(
                "DELETE FROM `email_verifications` WHERE `uid` = ? AND `used` IS NULL",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to revoke email verification tokens: " . $e->getMessage(), 0, $e);
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
                "DELETE FROM `email_verifications`
                 WHERE `expires` < NOW() - INTERVAL 7 DAY
                    OR `used` < NOW() - INTERVAL 7 DAY"
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to purge spent email verification tokens: " . $e->getMessage(), 0, $e);
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
