<?php
/**
 * Home page controller
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
 * Handles the home page of the application.
 */
class HomeController extends Controller
{
    /**
     * Display the homepage.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        try {
            View::renderTemplate('/App/Views/home.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the home page: " . $e->getMessage(), 0, $e);
        }
    }
}

