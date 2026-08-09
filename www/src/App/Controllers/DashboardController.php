<?php
/**
 * Dashboard page controller for signed-in users
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use Core\Model;
use App\Models\UsersModel;
use Core\Controller;

/**
 * Handles the dashboard page for logged-in users.
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        $this->requireLogin();

        $templateData = [];

        try {
            $templateData['total_users'] = UsersModel::count();
        } catch (\Throwable $e) {
            $templateData['total_users'] = 0;
            error_log("Failed to get total user count for dashboard: " . $e->getMessage());
        }

        if (ADMIN_LOGGED === true) {
            try {
                $activeUsersResult = Model::getSingleRow("SELECT COUNT(*) as count FROM `users` WHERE `status` = 'active'");
                $templateData['active_users'] = $activeUsersResult ? intval($activeUsersResult['count']) : 0;

                $inactiveUsersResult = Model::getSingleRow("SELECT COUNT(*) as count FROM `users` WHERE `status` != 'active'");
                $templateData['inactive_users'] = $inactiveUsersResult ? intval($inactiveUsersResult['count']) : 0;

                $newUsersResult = Model::getSingleRow("
                    SELECT COUNT(*) as count 
                    FROM `users` 
                    WHERE `registered` >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ");
                $templateData['new_users_30d'] = $newUsersResult ? intval($newUsersResult['count']) : 0;

            } catch (\Throwable $e) {
                $templateData['active_users'] = 0;
                $templateData['inactive_users'] = 0;
                $templateData['new_users_30d'] = 0;
                error_log("Failed to get dashboard statistics: " . $e->getMessage());
            }
        }

        try {
            View::renderTemplate('/App/Views/dashboard.php', $templateData);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render dashboard: " . $e->getMessage(), 0, $e);
        }
    }
}

