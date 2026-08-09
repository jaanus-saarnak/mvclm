<?php
/**
 * Application settings page controller
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use App\Flash;
use Core\Controller;

/**
 * Handles the application settings page.
 */
class AppSettingsController extends Controller
{
    /**
     * Display the application settings page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        $this->requireLogin();

        if (!defined('ADMIN_LOGGED') || !ADMIN_LOGGED) {
            Flash::addMessage('You must be an administrator to access application settings', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/dashboard');
            return;
        }

        try {
            View::renderTemplate('/App/Views/app-settings.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the application settings page: " . $e->getMessage(), 0, $e);
        }
    }
}

