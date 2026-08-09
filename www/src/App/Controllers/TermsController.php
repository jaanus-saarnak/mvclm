<?php
/**
 * Terms page controller
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
 * Handles the terms and conditions page display.
 */
class TermsController extends Controller
{
    /**
     * Display the terms and conditions page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        try {
            View::renderTemplate('/App/Views/terms.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the terms page: " . $e->getMessage(), 0, $e);
        }
    }
}

