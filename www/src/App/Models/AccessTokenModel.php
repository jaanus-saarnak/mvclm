<?php
/**
 * Access token model for remembered logins, stored in the sessions table
 *
 * An account holds one token at a time, so signing in on a second device signs
 * the first one out. Tokens older than seven calendar days are ignored, which
 * makes a remembered session last seven to eight days.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Core\Model;

/**
 * Handles storage and retrieval of user access tokens.
 */
class AccessTokenModel
{
    /**
     * Add a new access token for a user.
     *
     * @param int $uid The unique user ID
     * @param string $token The access token string
     * @return void
     * @throws RuntimeException If the database operation fails
     */
    public static function add(int $uid, string $token): void
    {
        self::delete($uid);

        try {
            Model::sqlQueryPrepared(
                "INSERT INTO `sessions` (`uid`, `accesstoken`, `created`) VALUES (?, ?, NOW())",
                [(string)$uid, $token]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to add access token: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get the most recent valid access token for a user.
     *
     * @param int $uid The unique user ID
     * @return string|bool The access token if found and valid, false otherwise
     * @throws RuntimeException If the database operation fails
     */
    public static function get(int $uid): string|bool
    {
        try {
            $result = Model::getSingleRowPrepared("
                SELECT * FROM `sessions`
                WHERE `uid` = ?
                AND `created` >= DATE(NOW()) - INTERVAL 7 DAY
                ORDER BY `id` DESC
                LIMIT 1
            ", [(string)$uid]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to get access token: " . $e->getMessage(), 0, $e);
        }

        if (isset($result['accesstoken']) && $result['accesstoken'] !== '') {
            return $result['accesstoken'];
        }

        return false;
    }

    /**
     * Delete all access tokens for a user.
     *
     * @param int $uid The unique user ID
     * @return void
     * @throws RuntimeException If the database operation fails
     */
    public static function delete(int $uid): void
    {
        try {
            Model::sqlQueryPrepared(
                "DELETE FROM `sessions` WHERE `uid` = ?",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to delete access token: " . $e->getMessage(), 0, $e);
        }
    }
}

