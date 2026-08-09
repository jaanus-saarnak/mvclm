<?php
/**
 * Users page controller for administrators
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use App\Models\UsersModel;
use App\Flash;
use Core\Controller;

/**
 * Handles the users listing page.
 */
class UsersController extends Controller
{
    /**
     * Display the users page with all registered users.
     *
     * @throws RuntimeException If loading the users or rendering the page fails
     * @return void
     */
    public function index(): void
    {
        $this->requireLogin();

        if (!defined('ADMIN_LOGGED') || !ADMIN_LOGGED) {
            Flash::addMessage('You must be an administrator to access the user list', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/dashboard');
            return;
        }

        try {
            $userProfiles = UsersModel::getAll();
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve user profiles: " . $e->getMessage(), 0, $e);
        }

        try {
            View::renderTemplate('/App/Views/users.php', [
                'user_profiles' => $userProfiles
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the users template: " . $e->getMessage(), 0, $e);
        }
    }
}

