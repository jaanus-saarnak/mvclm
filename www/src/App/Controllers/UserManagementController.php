<?php
/**
 * User management page controller for administrators
 *
 * Edits a user's access level and status. An admin cannot demote themselves,
 * which would otherwise be a one-click way to leave the site with no
 * administrator.
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
use App\Models\UsersModel;

/**
 * Handles user management functionality for administrators.
 */
class UserManagementController extends Controller
{
    /**
     * Display the user management page for editing a specific user.
     *
     * @throws RuntimeException If loading the user or rendering the page fails
     * @return void
     */
    public function edit(): void
    {
        $this->requireLogin();
        
        if (!defined('ADMIN_LOGGED') || ADMIN_LOGGED !== true) {
            Flash::addMessage('You must be an administrator to access this page', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/users');
            return;
        }

        $userId = isset($this->route_params['uid']) ? intval($this->route_params['uid']) : 0;
        
        if ($userId === 0) {
            Flash::addMessage('Invalid user ID', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/users');
            return;
        }

        try {
            $userProfile = UsersModel::getByID($userId);
            
            if ($userProfile === null) {
                Flash::addMessage('User not found', Flash::DANGER);
                $this->redirect(CONFIG['url'] . '/users');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateUser($userId);
                return;
            }

            View::renderTemplate('/App/Views/user-management.php', [
                'user_profile' => $userProfile
            ]);
            
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to load user management page: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update user access level and status.
     *
     * @param int $userId The user ID to update
     * @throws RuntimeException If the update fails
     * @return void
     */
    private function updateUser(int $userId): void
    {
        if (CONFIG['demo_mode']) {
            Flash::addMessage('This function is not enabled in demo mode', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/user-management/' . $userId);
            return;
        }

        $accessLevel = $this->getPostedString('access_level');
        $status = $this->getPostedString('status');

        $validAccessLevels = ['user', 'moderator', 'admin'];
        if (!in_array($accessLevel, $validAccessLevels)) {
            Flash::addMessage('Invalid access level', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/user-management/' . $userId);
            return;
        }

        $validStatuses = ['active', 'inactive', 'suspended', 'pending'];
        if (!in_array($status, $validStatuses)) {
            Flash::addMessage('Invalid status', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/user-management/' . $userId);
            return;
        }

        // Prevent admin from changing their own access level
        if (defined('LOGGED_USER_PROFILE') && 
            is_array(LOGGED_USER_PROFILE) && 
            intval(LOGGED_USER_PROFILE['uid']) === $userId && 
            $accessLevel !== 'admin') {
            Flash::addMessage('You cannot remove your own admin privileges', Flash::DANGER);
            $this->redirect(CONFIG['url'] . '/user-management/' . $userId);
            return;
        }

        try {
            Model::sqlQueryPrepared(
                "UPDATE `users` SET `access_level` = ?, `status` = ? WHERE `uid` = ?",
                [$accessLevel, $status, (string)$userId]
            );

            Flash::addMessage('User updated successfully', Flash::SUCCESS);
            $this->redirect(CONFIG['url'] . '/users');
            
        } catch (\Throwable $e) {
            Flash::addMessage('Failed to update user', Flash::DANGER);
            throw new RuntimeException("Failed to update user: " . $e->getMessage(), 0, $e);
        }
    }
}

