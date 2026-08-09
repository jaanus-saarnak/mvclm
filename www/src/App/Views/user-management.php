<?php
/**
 * User management page for administrators
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$layout = 'dashboard';
$title = 'User Management - ' . htmlspecialchars($content['user_profile']['username']) . ' - ' . CONFIG['app_name'];
$description = 'Manage user access levels and account status for ' . htmlspecialchars($content['user_profile']['username']) . ' on ' . CONFIG['app_name'];
?>

<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        User Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Edit user access level and status
                    </p>
                </div>
                <div>
                    <a href="<?php echo CONFIG['url']; ?>/users" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xs rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">User Information</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Username</p>
                        <p class="mt-1 text-sm text-gray-900">
                            <?php echo htmlspecialchars($content['user_profile']['username']); ?>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-sm text-gray-900">
                            <?php echo htmlspecialchars($content['user_profile']['email']); ?>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">User ID</p>
                        <p class="mt-1 text-sm text-gray-900">
                            <?php echo $content['user_profile']['uid']; ?>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Registered</p>
                        <p class="mt-1 text-sm text-gray-900">
                            <?php echo date_format(date_create($content['user_profile']['registered']), 'F d, Y'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xs rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit User Settings</h2>
            </div>
            
            <form method="POST" action="<?php echo CONFIG['url']; ?>/user-management/<?php echo $content['user_profile']['uid']; ?>">
                <?php echo \Core\Csrf::field(); ?>
                <div class="p-6 space-y-6">
                    
                    <div>
                        <label for="access_level" class="block text-sm font-medium text-gray-700">
                            Access Level
                        </label>
                        <select id="access_level" name="access_level" 
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-xs focus:outline-hidden focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="user" <?php echo $content['user_profile']['access_level'] === 'user' ? 'selected' : ''; ?>>
                                User
                            </option>
                            <option value="moderator" <?php echo $content['user_profile']['access_level'] === 'moderator' ? 'selected' : ''; ?>>
                                Moderator
                            </option>
                            <option value="admin" <?php echo $content['user_profile']['access_level'] === 'admin' ? 'selected' : ''; ?>>
                                Administrator
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Administrators have full access to all features including user management.
                        </p>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Account Status
                        </label>
                        <select id="status" name="status" 
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-xs focus:outline-hidden focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="active" <?php echo $content['user_profile']['status'] === 'active' ? 'selected' : ''; ?>>
                                Active
                            </option>
                            <option value="inactive" <?php echo $content['user_profile']['status'] === 'inactive' ? 'selected' : ''; ?>>
                                Inactive
                            </option>
                            <option value="suspended" <?php echo $content['user_profile']['status'] === 'suspended' ? 'selected' : ''; ?>>
                                Suspended
                            </option>
                            <option value="pending" <?php echo $content['user_profile']['status'] === 'pending' ? 'selected' : ''; ?>>
                                Pending
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Suspended and inactive users cannot log in to their accounts.
                        </p>
                    </div>
                    
                    <?php 
                    if (defined('LOGGED_USER_PROFILE') && 
                        is_array(LOGGED_USER_PROFILE) && 
                        intval(LOGGED_USER_PROFILE['uid']) === intval($content['user_profile']['uid'])) { 
                    ?>
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Warning
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>You are editing your own account. You cannot remove your own admin privileges.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <a href="<?php echo CONFIG['url']; ?>/users" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

