<?php
/**
 * Says which account statuses are kept out of the application
 *
 * Both doors ask here: the login form before it signs anyone in, and index.php
 * on every request, since a status can change while someone is already inside.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App;

/**
 * The authentication half of a user's status column.
 */
class AccountStatus
{
    /**
     * The statuses an administrator sets to mean "not in the application".
     *
     * 'pending' is deliberately absent. Registration never sets it, the column
     * defaults to 'active', and there is no approval flow to move an account
     * out of it, so blocking it would leave a person waiting for a step nobody
     * can take. An address that has not been confirmed is email verification's
     * job, and that gate is separate.
     */
    private const BLOCKED = ['suspended', 'inactive'];

    /**
     * Whether an account with this status is barred from the application.
     *
     * @param string $status The value of the user's status column
     * @return bool True if this account must not be signed in
     */
    public static function isBlocked(string $status): bool
    {
        return in_array($status, self::BLOCKED, true);
    }

    /**
     * What to tell someone their account's status means.
     *
     * Saying it plainly costs nothing. Whoever reads this has already given the
     * right password, or is holding a session that was granted for one, so the
     * status is not news to an attacker who does not have those.
     *
     * @param string $status The value of the user's status column
     * @return string A sentence for a flash message
     */
    public static function message(string $status): string
    {
        return match ($status) {
            'suspended' => 'This account has been suspended.',
            'inactive' => 'This account is inactive.',
            default => 'This account is not available.',
        };
    }
}
