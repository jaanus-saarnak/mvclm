<?php
/**
 * Account settings page
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$layout = 'dashboard';
$title = 'Account Settings - ' . CONFIG['app_name'];
$description = 'Manage your account settings, view account information, and control your profile on ' . CONFIG['app_name'];
?>

<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-3">
            <p class="mb-2 text-gray-600">Account settings</p>
        </div>
        
        <div class="bg-white shadow-xs rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Information</h3>
            </div>
            
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="flex items-center">
                        <input type="text" value="<?php echo htmlspecialchars(LOGGED_USER_PROFILE['username']); ?>" 
                               class="flex-1 py-2 px-3 border border-gray-300 rounded-md shadow-xs text-gray-500 bg-gray-50" 
                               disabled>
                        <span class="ml-3 text-sm text-gray-500">Cannot be changed</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="flex items-center">
                        <input type="text" value="<?php echo htmlspecialchars(LOGGED_USER_PROFILE['email']); ?>" 
                               class="flex-1 py-2 px-3 border border-gray-300 rounded-md shadow-xs text-gray-500 bg-gray-50" 
                               disabled>
                        <span class="ml-3 text-sm text-gray-500">Cannot be changed</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                    <p class="text-sm text-gray-900">
                        <?php echo date_format(date_create(LOGGED_USER_PROFILE['registered']), 'F d, Y'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white shadow-xs rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-red-800">Danger Zone</h3>
            </div>
            
            <div class="p-6">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-medium text-gray-900">Delete Account</h4>
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                        <div class="mt-4">
                            <form method="post" action="<?php echo CONFIG['url']; ?>/delete/<?php echo LOGGED_USER_PROFILE['uid']; ?>"
                                  onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone!')">
                                <?php echo \Core\Csrf::field(); ?>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete My Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

