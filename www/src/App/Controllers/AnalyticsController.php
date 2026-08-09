<?php
/**
 * Analytics page controller for administrators
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use Core\Controller;
use Core\Model;
use App\Flash;

/**
 * Handles analytics dashboard functionality.
 */
class AnalyticsController extends Controller
{
    /**
     * Displays the analytics dashboard.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        $this->requireLogin();
        
        if (!defined('ADMIN_LOGGED') || !ADMIN_LOGGED) {
            Flash::addMessage('You must be an administrator to access analytics', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/dashboard');
            return;
        }

        try {
            $templateData = $this->gatherAnalyticsData();
            View::renderTemplate('/App/Views/analytics.php', $templateData);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf('Failed to render analytics page: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * Gather all analytics data for the dashboard.
     */
    private function gatherAnalyticsData(): array
    {
        return [
            'stats' => $this->getKeyMetrics(),
            'growth_labels' => $this->getGrowthLabels(),
            'growth_data' => $this->getGrowthData(),
            'status_distribution' => $this->getStatusDistribution(),
            'access_levels' => $this->getAccessLevelDistribution(),
            'registration_labels' => $this->getRegistrationLabels(),
            'registration_data' => $this->getRegistrationData(),
            'recent_activities' => $this->getRecentActivities(),
            'recently_active' => $this->getRecentlyActiveUsers()
        ];
    }

    /**
     * Get key metrics for the dashboard.
     */
    private function getKeyMetrics(): array
    {
        $stats = [];

        $result = Model::getSingleRow("SELECT COUNT(*) as count FROM users");
        $stats['total_users'] = $result ? intval($result['count']) : 0;

        $result = Model::getSingleRow("
            SELECT COUNT(*) as count FROM users
            WHERE DATE(last_login) = CURDATE()
            AND last_login > registered
        ");
        $stats['active_today'] = $result ? intval($result['count']) : 0;

        $result = Model::getSingleRow("
            SELECT COUNT(*) as count FROM users 
            WHERE registered >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stats['new_users_30d'] = $result ? intval($result['count']) : 0;

        return $stats;
    }

    /**
     * Get the month names for the growth chart, oldest first.
     *
     * Keep "first day of". Plain "-1 month" from the 31st asks for a day the
     * previous month does not have and rolls forward, so on those dates the
     * axis repeats two months and drops two others.
     */
    private function getGrowthLabels(): array
    {
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $labels[] = date('M', strtotime("first day of -$i months"));
        }
        return $labels;
    }

    private function getGrowthData(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $endDate = date('Y-m-t', strtotime("first day of -$i months"));

            $result = Model::getSingleRowPrepared("
                SELECT COUNT(*) as count FROM users
                WHERE registered <= ?
            ", [$endDate]);
            $data[] = $result ? intval($result['count']) : 0;
        }
        return $data;
    }

    /**
     * Get user status distribution.
     */
    private function getStatusDistribution(): array
    {
        $statuses = [];
        $result = Model::getMultiRow("
            SELECT status, COUNT(*) as count 
            FROM users 
            GROUP BY status
        ");

        foreach ($result as $row) {
            $statuses[] = [
                'status' => ucfirst($row['status']),
                'count' => intval($row['count'])
            ];
        }

        return $statuses;
    }

    /**
     * Get access level distribution.
     */
    private function getAccessLevelDistribution(): array
    {
        $levels = [];
        $colors = [
            'admin' => 'bg-red-600',
            'moderator' => 'bg-yellow-600',
            'user' => 'bg-blue-600'
        ];

        $result = Model::getMultiRow("
            SELECT access_level, COUNT(*) as count 
            FROM users 
            GROUP BY access_level
        ");

        foreach ($result as $row) {
            $levels[] = [
                'level' => $row['access_level'],
                'count' => intval($row['count']),
                'color' => $colors[$row['access_level']] ?? 'bg-gray-600'
            ];
        }

        return $levels;
    }

    /**
     * Get the week labels for the registration chart, oldest first.
     */
    private function getRegistrationLabels(): array
    {
        $labels = [];
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = 'Week ' . (4 - $i);
        }
        return $labels;
    }

    private function getRegistrationData(): array
    {
        $data = [];
        for ($i = 3; $i >= 0; $i--) {
            $startDate = date('Y-m-d', strtotime("-" . (($i + 1) * 7) . " days +1 day"));
            $endDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days +1 day"));

            $result = Model::getSingleRowPrepared("
                SELECT COUNT(*) as count FROM users
                WHERE registered >= ? AND registered < ?
            ", [$startDate, $endDate]);
            $data[] = $result ? intval($result['count']) : 0;
        }
        return $data;
    }

    /**
     * Get recent user activities.
     *
     * Sort on the raw datetime, never on the rendered phrase. strtotime()
     * cannot read "Just now" and returns false, which sorts the newest entry
     * to the bottom where array_slice() cuts it.
     */
    private function getRecentActivities(): array
    {
        $activities = [];
        
        $recentUsers = Model::getMultiRow("
            SELECT username, registered FROM users
            ORDER BY registered DESC
            LIMIT 8
        ");

        foreach ($recentUsers as $user) {
            $activities[] = [
                'text' => $user['username'] . ' registered',
                'time' => $this->timeAgo($user['registered']),
                'timestamp' => strtotime($user['registered']),
                'color' => 'bg-green-500',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>'
            ];
        }

        $recentLogins = Model::getMultiRow("
            SELECT username, last_login FROM users
            WHERE last_login > registered
            ORDER BY last_login DESC
            LIMIT 8
        ");

        foreach ($recentLogins as $user) {
            $activities[] = [
                'text' => $user['username'] . ' logged in',
                'time' => $this->timeAgo($user['last_login']),
                'timestamp' => strtotime($user['last_login']),
                'color' => 'bg-blue-500',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>'
            ];
        }

        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return array_slice($activities, 0, 8);
    }

    /**
     * Get the active accounts that logged in most recently.
     *
     * Named for what the query does. It was called getTopActiveUsers and sorted
     * by last_login, which is recency rather than frequency, and the frequency
     * it displayed was a random number.
     *
     * Keep the last_login > registered test. Registration does not set the
     * column, so without it a signup that never returned leads this list.
     */
    private function getRecentlyActiveUsers(): array
    {
        $recentUsers = Model::getMultiRow("
            SELECT username, last_login
            FROM users
            WHERE status = 'active'
            AND last_login > registered
            ORDER BY last_login DESC
            LIMIT 5
        ");

        $users = [];
        foreach ($recentUsers as $user) {
            $users[] = [
                'username' => $user['username'],
                'last_login' => $this->timeAgo($user['last_login'])
            ];
        }

        return $users;
    }

    /**
     * Convert datetime to human-readable time ago format.
     */
    private function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = round($diff / 60);
            return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = round($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = round($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }
}

