<?php
/**
 * Users model for the users table
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Models;

use Core\Model;
use RuntimeException;

/**
 * Handles user-related database operations.
 */
final class UsersModel
{
    /**
     * Add a new user to the database.
     *
     * @param string $username The desired username
     * @param string $password The plaintext password
     * @param string $email The user's email address
     * @param string $userIp The user's IP address at registration
     * @return void
     * @throws RuntimeException If the insertion fails
     */
    public static function add(string $username, string $password, string $email, string $userIp): void
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Failed to hash the user password.');
        }

        try {
            Model::sqlQueryPrepared(
                "INSERT INTO `users` (`username`, `password_hash`, `email`, `last_login_ip`) VALUES (?, ?, ?, ?)",
                [$username, $passwordHash, $email, $userIp]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to add a new user: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Retrieve a paginated set of user profiles.
     *
     * @param int $usersFrom The offset to start from
     * @param int $showResults The number of records to retrieve
     * @return array Array of user profiles
     * @throws RuntimeException If the query fails
     */
    public static function get(int $usersFrom, int $showResults): array
    {
        try {
            $userProfiles = Model::getMultiRowPrepared(
                "SELECT * FROM `users` LIMIT ?, ?",
                [(string)$usersFrom, (string)$showResults]
            );
            return is_array($userProfiles) ? $userProfiles : [];
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to retrieve users: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Retrieve all user profiles.
     *
     * @return array Array of all user profiles
     * @throws RuntimeException If the query fails
     */
    public static function getAll(): array
    {
        try {
            $userProfiles = Model::getMultiRow("SELECT * FROM `users` ORDER BY `registered` DESC");
            return is_array($userProfiles) ? $userProfiles : [];
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to retrieve all users: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Count the total number of users.
     *
     * @return int The total number of users
     * @throws RuntimeException If the query fails
     */
    public static function count(): int
    {
        try {
            $result = Model::getSingleRow("SELECT COUNT(`uid`) as users_count FROM `users`");
            if (!is_array($result) || !isset($result['users_count'])) {
                throw new RuntimeException('Unexpected result format when counting users.');
            }
            return (int)$result['users_count'];
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to count users: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete a user by their UID.
     *
     * @param int $uid The user's unique identifier
     * @return void
     * @throws RuntimeException If the deletion fails
     */
    public static function delete(int $uid): void
    {
        try {
            Model::sqlQueryPrepared(
                "DELETE FROM `users` WHERE `uid` = ?",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to delete user with UID {$uid}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Retrieve a user by username.
     *
     * @param string $username The username to search for
     * @return array|null User data if found, null otherwise
     * @throws RuntimeException If the query fails
     */
    public static function getByUsername(string $username): ?array
    {
        try {
            $userProfile = Model::getSingleRowPrepared(
                "SELECT * FROM `users` WHERE `username` = ? LIMIT 1",
                [$username]
            );
            return is_array($userProfile) ? $userProfile : null;
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve user by username '{$username}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Retrieve a user by email address.
     *
     * @param string $email The email to search for
     * @return array|null User data if found, null otherwise
     * @throws RuntimeException If the query fails
     */
    public static function getByEmail(string $email): ?array
    {
        try {
            $userProfile = Model::getSingleRowPrepared(
                "SELECT * FROM `users` WHERE `email` = ? LIMIT 1",
                [$email]
            );
            return is_array($userProfile) ? $userProfile : null;
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve user by email '{$email}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Retrieve a user by their UID.
     *
     * @param int $uid The user's unique identifier
     * @return array|null User data if found, null otherwise
     * @throws RuntimeException If the query fails
     */
    public static function getByID(int $uid): ?array
    {
        try {
            $userProfile = Model::getSingleRowPrepared(
                "SELECT * FROM `users` WHERE `uid` = ? LIMIT 1",
                [(string)$uid]
            );
            return is_array($userProfile) ? $userProfile : null;
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve user by UID {$uid}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get the username for a given UID.
     *
     * @param int $uid The user's unique identifier
     * @return string|null The username if found, null otherwise
     * @throws RuntimeException If the query fails
     */
    public static function usernameFromUid(int $uid): ?string
    {
        try {
            $result = Model::getSingleRowPrepared(
                "SELECT `username` FROM `users` WHERE `uid` = ? LIMIT 1",
                [(string)$uid]
            );
            if (is_array($result) && isset($result['username'])) {
                return (string)$result['username'];
            }
            return null;
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve username by UID {$uid}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Replace a user's password.
     *
     * Takes the plaintext and hashes it here, the way add() does, so that no
     * caller ever handles a hash and there is one place the hashing algorithm
     * is named.
     *
     * @param int $uid The user's unique identifier
     * @param string $password The new plaintext password
     * @return void
     * @throws RuntimeException If the hash or the update fails
     */
    public static function updatePassword(int $uid, string $password): void
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Failed to hash the new password.');
        }

        try {
            Model::sqlQueryPrepared(
                "UPDATE `users` SET `password_hash` = ? WHERE `uid` = ?",
                [$passwordHash, (string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to update the password for UID {$uid}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Record that a user's email address has been confirmed.
     *
     * Only ever sets the column, never clears it. Confirming twice leaves the
     * first timestamp alone, so the value stays the moment the address was
     * actually reached.
     *
     * @param int $uid The user's unique identifier
     * @return void
     * @throws RuntimeException If the update fails
     */
    public static function markEmailVerified(int $uid): void
    {
        try {
            Model::sqlQueryPrepared(
                "UPDATE `users` SET `email_verified_at` = NOW() WHERE `uid` = ? AND `email_verified_at` IS NULL",
                [(string)$uid]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to mark email verified for UID {$uid}: " . $e->getMessage(), 0, $e);
        }
    }
}

