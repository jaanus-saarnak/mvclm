<?php
/**
 * The user settings page controller
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use Core\Controller;

/**
 * Handles the user settings page.
 */
class SettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        $this->requireLogin();
        
        try {
            View::renderTemplate('/App/Views/settings.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the settings page: " . $e->getMessage(), 0, $e);
        }
    }
}

