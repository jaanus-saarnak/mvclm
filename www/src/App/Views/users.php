<?php
/**
 * Users page for administrators
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$layout = 'dashboard';
$title = 'Users - ' . CONFIG['app_name'];
$description = 'View and manage all registered users on ' . CONFIG['app_name'];
?>

<main id="content" class="">
    <div class="lg:pb-3 pt-10 w-full max-w-6xl px-4 sm:px-6 lg:px-8 mx-auto">

        <div class="mb-3">
            <p class="mb-2 text-gray-600">All registered users</p>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white shadow-xs rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-6 overflow-x-auto">
                <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Username
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Access Level
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registered
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Login
                            </th>
                            <?php if (ADMIN_LOGGED) { ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        foreach ($content['user_profiles'] as $row) {
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php 
                                        $firstLetter = strtoupper(substr($row['username'], 0, 1));
                                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                                        $colorIndex = ord($firstLetter) % count($colors);
                                        $bgColor = $colors[$colorIndex];
                                        ?>
                                        <div class="shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full <?php echo $bgColor; ?> flex items-center justify-center text-white font-semibold">
                                                <?php echo htmlspecialchars($firstLetter); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="<?php echo CONFIG['url'] . "/" . htmlspecialchars($row['username']); ?>" 
                                               class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                                <?php echo htmlspecialchars($row['username']); ?>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $accessColors = [
                                        'user' => 'bg-gray-100 text-gray-800',
                                        'moderator' => 'bg-blue-100 text-blue-800',
                                        'admin' => 'bg-purple-100 text-purple-800'
                                    ];
                                    $accessLevel = $row['access_level'];
                                    $colorClasses = $accessColors[$accessLevel] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $colorClasses; ?>">
                                        <?php echo htmlspecialchars(ucfirst($accessLevel)); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'suspended' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800'
                                    ];
                                    $status = $row['status'];
                                    $statusColorClasses = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColorClasses; ?>">
                                        <?php echo htmlspecialchars(ucfirst($status)); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-order="<?php echo strtotime($row['registered']); ?>">
                                    <div class="text-sm text-gray-500">
                                        <?php echo date_format(date_create($row['registered']), 'M d, Y'); ?>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        <?php echo date_format(date_create($row['registered']), 'g:i A'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-order="<?php echo strtotime($row['last_login']); ?>">
                                    <div class="text-sm text-gray-500">
                                        <?php echo date_format(date_create($row['last_login']), 'M d, Y'); ?>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        <?php echo date_format(date_create($row['last_login']), 'g:i A'); ?>
                                    </div>
                                </td>
                                <?php if (ADMIN_LOGGED) { ?>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="<?php echo CONFIG['url'] . '/user-management/' . intval($row['uid']); ?>"
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (empty($content['user_profiles'])) { ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<link rel="stylesheet" type="text/css" href="/assets/vendor/datatables/dataTables.tailwindcss.min.css">

<script type="text/javascript" charset="utf8" src="/assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="/assets/vendor/datatables/dataTables.tailwindcss.min.js"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        scrollX: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[3, 'desc']],
        language: {
            search: "Search users:",
            lengthMenu: "Show _MENU_ users per page",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "Showing 0 to 0 of 0 users",
            infoFiltered: "(filtered from _MAX_ total users)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"mb-2 sm:mb-0"f><"mb-2 sm:mb-0"l>>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4"<"mb-2 sm:mb-0"i><"mb-2 sm:mb-0"p>>',
        drawCallback: function() {
            $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 mx-1 text-sm border border-gray-300 rounded-sm hover:bg-gray-100');
            $('.dataTables_paginate .paginate_button.current').addClass('bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700');
            $('.dataTables_paginate .paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');
        }
    });
    
    $('div.dataTables_filter input').addClass('py-2 px-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ml-2');
    $('div.dataTables_length select').addClass('py-2 px-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm mx-2');
});
</script>

<style>
.dataTables_wrapper {
    width: 100%;
}

.dataTables_scrollBody::-webkit-scrollbar {
    height: 8px;
}

.dataTables_scrollBody::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dataTables_scrollBody::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
    background: #555;
}

@media (max-width: 768px) {
    .dataTables_scrollBody {
        box-shadow: inset -10px 0 10px -10px rgba(0,0,0,0.1);
    }
}
</style>

