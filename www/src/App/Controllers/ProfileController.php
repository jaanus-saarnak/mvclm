<?php
/**
 * Profile page controller
 *
 * Handles the public profile and account deletion. The profile route is the
 * catch-all registered last in routes.php, so any URL matching nothing else is
 * read as a username. Suspended and inactive accounts answer 404, which keeps
 * their status hidden from everyone but an admin and the account owner.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use App\Flash;
use App\Models\UsersModel;
use Core\Controller;

/**
 * Handles user profile display and user deletion.
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     *
     * @throws RuntimeException If retrieving user data or rendering templates fails
     * @return void
     */
    public function index(): void
    {
        try {
            $userProfile = UsersModel::getByUsername($this->route_params['username']);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve user profile: " . $e->getMessage(), 0, $e);
        }

        if (!is_array($userProfile)) {
            try {
                http_response_code(404);
                View::renderTemplate('/App/Views/404.php', []);
            } catch (\Throwable $e) {
                throw new RuntimeException("Failed to render 404 template: " . $e->getMessage(), 0, $e);
            }
            return;
        }

        if (CONFIG['require_login_to_view_profile']) {
            try {
                $this->requireLogin();
            } catch (\Throwable $e) {
                throw new RuntimeException("Login required to view profile: " . $e->getMessage(), 0, $e);
            }
        }
        
        // Hide profiles of suspended/inactive users from non-admin users
        if (!defined('ADMIN_LOGGED') || ADMIN_LOGGED !== true) {
            if (in_array($userProfile['status'], ['suspended', 'inactive'])) {
                // Only show suspended/inactive profiles to admins or the user themselves
                if (!defined('USER_LOGGED') || 
                    !defined('LOGGED_USER_PROFILE') || 
                    !is_array(LOGGED_USER_PROFILE) ||
                    LOGGED_USER_PROFILE['uid'] !== $userProfile['uid']) {
                    try {
                        http_response_code(404);
                        View::renderTemplate('/App/Views/404.php', []);
                        return;
                    } catch (\Throwable $e) {
                        throw new RuntimeException("Failed to render 404 template: " . $e->getMessage(), 0, $e);
                    }
                }
            }
        }
        
        $usersWithCommonBrowser = [];

        if (defined('ADMIN_LOGGED') && ADMIN_LOGGED === true) {
            try {
                $usersWithCommonBrowser = self::getUsersWithCommonBrowser(
                    $userProfile['logins_cookie_stats'] ?? '',
                    intval($userProfile['uid'])
                );
            } catch (\Throwable $e) {
                throw new RuntimeException("Failed to get users with common browser: " . $e->getMessage(), 0, $e);
            }
        }

        try {
            View::renderTemplate('/App/Views/user.php', [
                'user_profile' => $userProfile,
                'users_with_common_browser' => $usersWithCommonBrowser
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render user template: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete a user based on the 'uid' route parameter.
     *
     * Requires a POST. Do not remove that test. The route pattern matches any
     * method, and the CSRF gate in index.php only examines POSTs, so a GET
     * would reach this action as a working one-click link.
     *
     * @throws RuntimeException If deleting the user fails
     * @return void
     */
    public function delete(): void
    {
        $this->requireLogin();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            Flash::addMessage('Use the Delete My Account button to remove your account', Flash::WARNING);
            $this->redirect(CONFIG['url'] . '/settings');
        }

        $uidParam = $this->route_params['uid'] ?? null;

        if (!CONFIG['demo_mode'] && isset($uidParam)) {
            $deleted = false;
            
            // Admin can delete any user
            if (defined('ADMIN_LOGGED') && ADMIN_LOGGED === true) {
                try {
                    UsersModel::delete(intval($uidParam));
                    $deleted = true;
                } catch (\Throwable $e) {
                    throw new RuntimeException("Failed to delete user as admin: " . $e->getMessage(), 0, $e);
                }
            }
            // Users can delete their own account
            elseif (defined('USER_LOGGED') && USER_LOGGED === true &&
                    defined('LOGGED_USER_PROFILE') &&
                    is_array(LOGGED_USER_PROFILE) &&
                    isset(LOGGED_USER_PROFILE['uid']) &&
                    intval($uidParam) === intval(LOGGED_USER_PROFILE['uid'])) {
                try {
                    UsersModel::delete(intval(LOGGED_USER_PROFILE['uid']));
                    LoginController::performLogout();
                    return; // performLogout() handles redirect
                } catch (\Throwable $e) {
                    throw new RuntimeException("Failed to delete self-user and logout: " . $e->getMessage(), 0, $e);
                }
            }
            
            if ($deleted) {
                Flash::addMessage('User deleted', Flash::SUCCESS, 3000);
            }
        } else if (CONFIG['demo_mode']) {
            Flash::addMessage('This function is not enabled in demo mode', Flash::DANGER, 100000);
        }

        $this->redirect(CONFIG['url']);
    }

    /**
     * Retrieve users who share the same browser login patterns.
     *
     * @param string $uidsFromCookieStats Comma-separated list of user IDs
     * @param int $profileUid The UID of the current profile
     * @throws RuntimeException If fetching usernames fails
     * @return array<int,object> Array of stdClass objects representing users
     */
    public static function getUsersWithCommonBrowser(string $uidsFromCookieStats, int $profileUid): array
    {
        $userIdsArray = explode(",", $uidsFromCookieStats);
        $return = [];
        
        foreach ($userIdsArray as $uid) {
            $uid = trim($uid);

            if ($uid !== '' && intval($uid) !== $profileUid && intval($uid) !== 0) {
                try {
                    $username = UsersModel::usernameFromUid(intval($uid));
                } catch (\Throwable $e) {
                    throw new RuntimeException("Failed to fetch username for UID {$uid}: " . $e->getMessage(), 0, $e);
                }

                $user = new \stdClass();
                $user->uid = intval($uid);
                $user->username = $username;
                $user->user_deleted = ($username === null);

                $return[] = $user;
            }
        }

        return $return;
    }
}

