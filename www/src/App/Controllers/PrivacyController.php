<?php
/**
 * Privacy page controller
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
 * Handles the privacy page display.
 */
class PrivacyController extends Controller
{
    /**
     * Display the privacy page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        try {
            View::renderTemplate('/App/Views/privacy.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the privacy page: " . $e->getMessage(), 0, $e);
        }
    }
}

