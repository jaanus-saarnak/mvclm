<?php
/**
 * About page controller
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
 * Handles the about page display.
 */
class AboutController extends Controller
{
    /**
     * Display the about page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        try {
            View::renderTemplate('/App/Views/about.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the about page: " . $e->getMessage(), 0, $e);
        }
    }
}

