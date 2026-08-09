<?php
/**
 * Login model for attempt records and the brute-force counter
 *
 * Every attempt, right or wrong, is written to login_ip.
 * countWrongPasswordsFromIp() is not a pure read: it first deletes every row in
 * the table older than 24 hours, for all addresses and not just the one being
 * counted, so the log trims itself whenever anyone signs in. This model also
 * writes last_login and logins_cookie_stats on the users table.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use Core\Model;

/**
 * Handles login-related database operations.
 */
class LoginModel
{
    public const CORRECT = 'correct';
    public const WRONG = 'wrong';

    /**
     * Save a login attempt.
     *
     * @param string $ip The IP address from which the login attempt was made
     * @param int $uid The unique user ID
     * @param string $password The password status ('correct' or 'wrong')
     * @return void
     * @throws RuntimeException If the database operation fails
     */
    public static function saveLoginIp(string $ip, int $uid, string $password): void
    {
        try {
            Model::sqlQueryPrepared(
                "INSERT INTO `login_ip` (`uid`, `ip`, `password`) VALUES (?, ?, ?)",
                [(string)$uid, $ip, $password]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to save login IP attempt: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Count wrong password attempts from an IP address in the last 24 hours.
     *
     * @param string $ip The IP address to check
     * @return int The number of wrong password attempts
     * @throws RuntimeException If the database operation fails
     */
    public static function countWrongPasswordsFromIp(string $ip): int
    {
        try {
            // Remove entries older than 24 hours
            Model::sqlQuery("DELETE FROM `login_ip` WHERE `time_added` < DATE_SUB(NOW(), INTERVAL 24 HOUR)");

            $result = Model::getSingleRowPrepared(
                "SELECT COUNT(`id`) AS passwords_count FROM `login_ip` WHERE `ip` = ? AND `password` = ?",
                [$ip, self::WRONG]
            );

            if (!is_array($result) || !isset($result['passwords_count'])) {
                return 0;
            }

            return (int)$result['passwords_count'];
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to count wrong passwords from IP: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update login statistics for a user.
     *
     * @param int $uid The unique user ID
     * @param string $updatedUids Comma-separated UIDs for browser tracking
     * @return void
     * @throws RuntimeException If the database operation fails
     */
    public static function updateLoginStats(int $uid, string $updatedUids): void
    {
        try {
            Model::sqlQueryPrepared(
                "UPDATE `users` SET `logins_cookie_stats` = ? WHERE `uid` = ?",
                [$updatedUids, (string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to update login stats: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update the last login time for a user.
     *
     * @param int $uid The unique user ID
     * @return void
     * @throws RuntimeException If the database operation fails
     */
    public static function updateLastLoginTime(int $uid): void
    {
        try {
            Model::sqlQueryPrepared(
                "UPDATE `users` SET `last_login` = NOW() WHERE `uid` = ?",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to update last login time: " . $e->getMessage(), 0, $e);
        }
    }
}

