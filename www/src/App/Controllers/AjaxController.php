<?php
/**
 * Ajax controller for live username checking on the registration form
 *
 * Answers in plain text, not JSON, and says nothing at all when the name is
 * free. The browser treats an empty response as available, so silence is the
 * success case, and an empty field is silent because nothing has been typed yet.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use App\Models\UsersModel;
use Core\Controller;

/**
 * Handles AJAX requests for username validation.
 */
class AjaxController extends Controller
{
    /**
     * Validates a username received via POST request.
     *
     * @throws RuntimeException If database error occurs
     * @return void
     */
    public function username(): void
    {
        $postedUsername = $this->getPostedString('username');

        if ($postedUsername === false) {
            return;
        }

        if ($postedUsername === '') {
            return;
        }

        if (!preg_match("/^[A-Za-z0-9]+$/", $postedUsername)) {
            echo 'Illegal character in username';
            return;
        }

        $length = strlen($postedUsername);

        if ($length < 3) {
            echo 'Username should be at least 3 characters';
            return;
        }

        if ($length > 16) {
            echo 'Username should be at most 16 characters';
            return;
        }

        try {
            $existingUser = UsersModel::getByUsername($postedUsername);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to check username availability: " . $e->getMessage(), 0, $e);
        }

        if ($existingUser !== null) {
            echo 'Username taken';
        }
    }
}

