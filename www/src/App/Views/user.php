<?php
/**
 * User profile page
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = htmlspecialchars($content['user_profile']['username']) . ' - ' . CONFIG['app_name'];
$description = 'View the profile of ' . htmlspecialchars($content['user_profile']['username']) . ' on ' . CONFIG['app_name'];
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-16 w-full max-w-5xl px-4 sm:px-6 lg:px-8 mx-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-xs rounded-lg border border-gray-200 p-6">
                    <div class="text-center">
                        <?php 
                        $firstLetter = strtoupper(substr($content['user_profile']['username'], 0, 1));
                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                        $colorIndex = ord($firstLetter) % count($colors);
                        $bgColor = $colors[$colorIndex];
                        ?>
                        <div class="inline-flex items-center justify-center w-32 h-32 rounded-full <?php echo $bgColor; ?> text-white text-4xl font-bold mb-4">
                            <?php echo $firstLetter; ?>
                        </div>
                        <h1 class="text-xl font-semibold text-gray-900">
                            <?php echo htmlspecialchars($content['user_profile']['username']); ?>
                        </h1>
                        
                        <?php if (ADMIN_LOGGED) { ?>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-gray-800">
                                    User ID: <?php echo $content['user_profile']['uid']; ?>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-xs rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="sm:w-1/3">
                                <p class="text-sm font-medium text-gray-500">Email</p>
                            </div>
                            <div class="mt-1 sm:mt-0 sm:w-2/3">
                                <p class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars($content['user_profile']['email']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="sm:w-1/3">
                                <p class="text-sm font-medium text-gray-500">Registered</p>
                            </div>
                            <div class="mt-1 sm:mt-0 sm:w-2/3">
                                <p class="text-sm text-gray-900">
                                    <?php echo date_format(date_create($content['user_profile']['registered']), 'F d, Y \a\t g:i A'); ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="sm:w-1/3">
                                <p class="text-sm font-medium text-gray-500">Last login</p>
                            </div>
                            <div class="mt-1 sm:mt-0 sm:w-2/3">
                                <p class="text-sm text-gray-900">
                                    <?php echo date_format(date_create($content['user_profile']['last_login']), 'F d, Y \a\t g:i A'); ?>
                                </p>
                            </div>
                        </div>

                        <?php if (ADMIN_LOGGED && !empty($content['user_profile']['last_login_ip'])) { ?>
                            <div class="flex flex-col sm:flex-row sm:items-center">
                                <div class="sm:w-1/3">
                                    <p class="text-sm font-medium text-gray-500">Last login IP</p>
                                </div>
                                <div class="mt-1 sm:mt-0 sm:w-2/3">
                                    <p class="text-sm text-gray-900 font-mono">
                                        <?php echo htmlspecialchars($content['user_profile']['last_login_ip']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="sm:w-1/3">
                                <p class="text-sm font-medium text-gray-500">Access level</p>
                            </div>
                            <div class="mt-1 sm:mt-0 sm:w-2/3">
                                <?php 
                                $statusColors = [
                                    'user' => 'bg-green-100 text-green-800',
                                    'moderator' => 'bg-yellow-100 text-yellow-800',
                                    'admin' => 'bg-red-100 text-red-800'
                                ];
                                $accessLevel = $content['user_profile']['access_level'];
                                $colorClasses = $statusColors[$accessLevel] ?? 'bg-green-100 text-green-800';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $colorClasses; ?>">
                                    <?php echo ucfirst($accessLevel); ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="sm:w-1/3">
                                <p class="text-sm font-medium text-gray-500">Status</p>
                            </div>
                            <div class="mt-1 sm:mt-0 sm:w-2/3">
                                <?php 
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800',
                                    'suspended' => 'bg-red-100 text-red-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $status = $content['user_profile']['status'];
                                $colorClasses = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $colorClasses; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (ADMIN_LOGGED && !empty($content['users_with_common_browser'])) { ?>
            <!-- Admin Notice -->
            <div class="mt-8">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-amber-800">
                                Admin Notice: Shared Browser Detection
                            </h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p class="mb-2">The following users have logged in from the same browser as this user:</p>
                                <div class="flex flex-wrap gap-2">
                                    <?php
                                    foreach ($content['users_with_common_browser'] as $commonUser) {
                                        if ($commonUser->user_deleted) {
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path>
                                                </svg>
                                                Deleted User (UID: <?php echo $commonUser->uid; ?>)
                                            </span>
                                            <?php
                                        } else {
                                            ?>
                                            <a href="<?php echo CONFIG['url'] . '/' . htmlspecialchars($commonUser->username); ?>" 
                                               class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors">
                                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z"></path>
                                                </svg>
                                                <?php echo htmlspecialchars($commonUser->username); ?>
                                            </a>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

