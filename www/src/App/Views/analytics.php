<?php
/**
 * Analytics page for administrators
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$layout = 'dashboard';

$title = 'Analytics Dashboard - ' . CONFIG['app_name'];
$description = 'View comprehensive analytics including user activity, growth metrics, and system performance for ' . CONFIG['app_name'];
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Analytics Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Comprehensive overview of user activity and system performance
                </p>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?php echo number_format($content['stats']['total_users'] ?? 0); ?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Today</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?php echo number_format($content['stats']['active_today'] ?? 0); ?>
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm text-gray-600">
                                        <span><?php echo round(($content['stats']['active_today'] ?? 0) / max(($content['stats']['total_users'] ?? 1), 1) * 100); ?>%</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">New Users (30d)</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        <?php echo number_format($content['stats']['new_users_30d'] ?? 0); ?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Charts Section -->
    <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">User Growth</h2>
                    <div style="height: 300px;">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">User Status Distribution</h2>
                    <div style="height: 300px;">
                        <canvas id="userStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline & Top Users -->
    <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Activity</h2>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <?php 
                            $activities = $content['recent_activities'] ?? [];
                            foreach ($activities as $index => $activity): 
                            ?>
                            <li>
                                <div class="relative pb-8">
                                    <?php if ($index < count($activities) - 1): ?>
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <?php endif; ?>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white <?php echo $activity['color'] ?? 'bg-gray-400'; ?>">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <?php echo $activity['icon'] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>'; ?>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($activity['text'] ?? 'New activity'); ?></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time><?php echo $activity['time'] ?? 'Just now'; ?></time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recently Active</h2>
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200">
                            <?php
                            $recentlyActive = $content['recently_active'] ?? [];
                            foreach ($recentlyActive as $user):
                            ?>
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="shrink-0">
                                        <?php 
                                        $firstLetter = strtoupper(substr($user['username'] ?? 'U', 0, 1));
                                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500'];
                                        $colorIndex = ord($firstLetter) % count($colors);
                                        ?>
                                        <div class="h-8 w-8 rounded-full <?php echo $colors[$colorIndex]; ?> flex items-center justify-center text-white text-sm font-medium">
                                            <?php echo $firstLetter; ?>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?>
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            <?php echo htmlspecialchars($user['last_login'] ?? 'Never'); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <a href="<?php echo CONFIG['url'] . '/' . htmlspecialchars($user['username'] ?? ''); ?>" class="inline-flex items-center shadow-xs px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Level & Registration Stats -->
    <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Access Level Distribution</h2>
                    <div class="space-y-4">
                        <?php
                        $accessLevels = $content['access_levels'] ?? [];
                        $totalUsers = array_sum(array_column($accessLevels, 'count'));
                        foreach ($accessLevels as $level):
                            $count = $level['count'] ?? 0;
                            $percentage = $totalUsers > 0 ? (int) round(($count / $totalUsers) * 100) : 0;

                            // A group that has members must never read as nothing. Anything
                            // under half a percent rounds to zero, which would print 0% beside
                            // a real count and draw a bar of no width.
                            $isTiny = $percentage === 0 && $count > 0;
                            $percentageLabel = $isTiny ? '&lt;1' : $percentage;
                            $barWidth = $isTiny ? 1 : $percentage;
                        ?>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700"><?php echo ucfirst($level['level'] ?? 'Unknown'); ?></span>
                                <span class="text-sm text-gray-500"><?php echo number_format($count); ?> (<?php echo $percentageLabel; ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="<?php echo $level['color'] ?? 'bg-blue-600'; ?> h-2 rounded-full" style="width: <?php echo $barWidth; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Registration Trends</h2>
                    <div style="height: 250px;">
                        <canvas id="registrationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/chartjs/chart.umd.js"></script>
<script>
// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($content['growth_labels'] ?? []); ?>,
        datasets: [{
            label: 'Total Users',
            data: <?php echo json_encode($content['growth_data'] ?? []); ?>,
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                // These are people. Without this the axis fills a small range
                // with fractions: 0,1 0,2 0,3 and so on.
                ticks: { precision: 0 }
            }
        }
    }
});

// User Status Chart
const userStatusCtx = document.getElementById('userStatusChart').getContext('2d');
const userStatusLabels = <?php echo json_encode(array_column($content['status_distribution'] ?? [], 'status')); ?>;

// Colour by status name, never by position. A status nobody holds is absent
// from the query, so a fixed list shifts every colour after the gap and
// Pending gets the red that means Suspended.
const userStatusColors = {
    Active: 'rgba(34, 197, 94, 0.8)',
    Inactive: 'rgba(156, 163, 175, 0.8)',
    Suspended: 'rgba(239, 68, 68, 0.8)',
    Pending: 'rgba(251, 191, 36, 0.8)'
};

new Chart(userStatusCtx, {
    type: 'doughnut',
    data: {
        labels: userStatusLabels,
        datasets: [{
            data: <?php echo json_encode(array_column($content['status_distribution'] ?? [], 'count')); ?>,
            backgroundColor: userStatusLabels.map(function (status) {
                return userStatusColors[status] || 'rgba(156, 163, 175, 0.8)';
            })
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Registration Trends Chart
const registrationCtx = document.getElementById('registrationChart').getContext('2d');
new Chart(registrationCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($content['registration_labels'] ?? []); ?>,
        datasets: [{
            label: 'New Registrations',
            data: <?php echo json_encode($content['registration_data'] ?? []); ?>,
            backgroundColor: 'rgba(99, 102, 241, 0.8)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

