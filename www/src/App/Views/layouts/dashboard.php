<?php
/**
 * Dashboard layout, the signed-in shell with the sidebar
 *
 * A view chooses this layout with $layout = 'dashboard'.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */
?>
<!DOCTYPE html>
<html lang="en" class="relative min-h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo htmlspecialchars($description ?? CONFIG['app_name']); ?>">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(\Core\Csrf::token(), ENT_QUOTES); ?>">
    <title><?php echo htmlspecialchars($title ?? CONFIG['app_name']); ?></title>

	<link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16x16.png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    
    <script src="/assets/vendor/jquery/jquery-1.11.1.min.js"></script>
    
    <link rel="stylesheet" href="/assets/css/app.css?v=<?php echo filemtime(MVCLM_ROOT . '/assets/css/app.css'); ?>">
</head>
<body class="h-full bg-gray-50 overflow-hidden">
    <div class="admin-container">
        <!-- Admin Top bar -->
        <div class="admin-topbar">
            <div class="flex items-center">
                <button id="openSidebar" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 mr-4 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <a class="flex-none rounded-md text-2xl inline-block font-semibold focus:outline-hidden focus:opacity-80 text-indigo-600" href="/dashboard" aria-label="<?php echo htmlspecialchars(CONFIG['app_name']); ?>">
                    <?php echo htmlspecialchars(CONFIG['app_name']); ?>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="hs-dropdown [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative inline-flex">
                    <button id="hs-pro-dnad" type="button" 
                        class="p-1.5 inline-flex items-center gap-x-2 text-base rounded-lg text-gray-800 hover:bg-gray-50 hover:text-indigo-600 focus:bg-gray-50" 
                        aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        <?php 
                        echo htmlspecialchars(ucfirst(LOGGED_USER_PROFILE['username']));
                        
                        $firstLetter = strtoupper(substr(LOGGED_USER_PROFILE['username'], 0, 1));
                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                        $colorIndex = ord($firstLetter) % count($colors);
                        $bgColor = $colors[$colorIndex];
                        ?>
                        <div class="shrink-0 size-7 rounded-full <?php echo $bgColor; ?> flex items-center justify-center text-white text-xs font-semibold">
                            <?php echo $firstLetter; ?>
                        </div>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-60 transition-[opacity,margin] duration opacity-0 hidden z-20 bg-white rounded-xl shadow-[0_10px_40px_10px_rgba(0,0,0,0.08)] absolute top-full right-0 mt-2" role="menu" aria-orientation="vertical" aria-labelledby="hs-pro-dnad-admin">
                        <div class="p-1">
                            <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-base text-gray-800 hover:bg-gray-50 hover:text-indigo-600 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden" 
                               href="/<?php echo LOGGED_USER_PROFILE['username']; ?>">
                                <svg class="shrink-0 mt-0.5 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
                                </svg>
                                My Profile
                            </a>
                            <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-base text-gray-800 hover:bg-gray-50 hover:text-indigo-600 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden" href="/settings">
                                <svg class="shrink-0 mt-0.5 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                My Settings
                            </a>
                        </div>

                        <div class="p-1 border-t border-gray-200">
                            <form method="post" action="<?php echo CONFIG['url']; ?>/logout">
                                <?php echo \Core\Csrf::field(); ?>
                                <button type="submit"
                                        class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-base text-gray-800 hover:bg-gray-50 hover:text-indigo-600 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50">
                                    <svg class="shrink-0 mt-0.5 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" x2="9" y1="12" y2="12" />
                                    </svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile backdrop -->
        <div id="mobileBackdrop" class="mobile-backdrop"></div>

        <?php
        $dashboardSidebarLinks = implode(' ', [
            'group',
            'flex',
            'items-center',
            'px-4',
            'py-2',
            'text-base',
            'text-gray-800',
            'hover:bg-gray-50',
            'hover:text-indigo-600',
            'rounded-md',
            'transition-colors'
        ]);
        ?>

        <!-- Admin Body -->
        <div class="admin-body">
            <!-- Sidebar -->
            <div id="adminSidebar" class="admin-sidebar">
                <nav class="px-2 py-4 space-y-1">
                    <a href="/dashboard" class="<?php echo $dashboardSidebarLinks; ?>">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </nav>

                <?php if (ADMIN_LOGGED) { ?>
                <nav class="border-t border-gray-200 flex-1 px-2 py-4 space-y-1">
                    <span class="pt-1 pb-1 px-3 block text-xs text-gray-500">Administrator</span>

                    <a href="/users" class="<?php echo $dashboardSidebarLinks; ?>">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Users
                    </a>
                    
                    <a href="/analytics" class="<?php echo $dashboardSidebarLinks; ?>">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analytics
                    </a>
                    
                    <a href="/app-settings" class="<?php echo $dashboardSidebarLinks; ?>">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                </nav>
                <?php } else { ?>
				<nav class="border-t border-gray-200 flex-1 px-2 py-4 space-y-1">
				</nav>
				<?php } ?>

                <div class="border-t border-gray-200 px-2 py-4">
                    <a href="/" class="<?php echo $dashboardSidebarLinks; ?>">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home
                    </a>

                    <a href="/about" class="<?php echo $dashboardSidebarLinks; ?> mt-1">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        About
                    </a>
                    
                    <a href="/contact" class="<?php echo $dashboardSidebarLinks; ?> mt-1">
                        <svg class="mr-4 h-6 w-6 text-gray-800 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact
                    </a>
                </div>
            </div>

            <!-- Main content area -->
            <div class="admin-main">
                <div class="admin-content">
                    <!-- Flash Messages -->
                    <?php
                    if (isset($flash_messages) && $flash_messages) {
                        ?>
                        <div class="flash-container">
                            <?php
                            foreach ($flash_messages as $flashMessage) {
                                ?>
                                <div class="alert alert-<?php echo htmlspecialchars($flashMessage['type']); ?> alert-auto-hide">
                                    <span><?php echo htmlspecialchars($flashMessage['body']); ?></span>
                                    <button class="alert-close" onclick="this.parentElement.remove()">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <script>
                        setTimeout(function() {
                            $('.alert').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, <?php echo $flash_messages[0]['timer']; ?>);
                        </script>
                        <?php
                    }
                    ?>

                    <!-- View Content -->
                    <?php echo $content; ?>
                </div>
                
                <!-- Dashboard Footer -->
                <footer class="admin-footer">
                    <div class="max-w-7xl p-2 sm:px-5 xl:px-8 sm:py-5 mx-auto w-full">
                        <div class="flex justify-between items-center">
                            <p class="text-xs sm:text-sm text-gray-500">
                                MIT License
                            </p>

                            <ul>
                                <li class="inline-block relative pe-5 text-xs sm:text-sm text-gray-500 align-middle last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-2 before:-translate-y-1/2 before:w-px before:h-3.5 before:bg-gray-400 before:rotate-[18deg]">
                                    <a class="hover:text-indigo-600 focus:outline-hidden focus:underline transition-colors" href="/terms">
                                        Terms
                                    </a>
                                </li>
                                <li class="inline-block relative pe-5 text-xs sm:text-sm text-gray-500 align-middle last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-2 before:-translate-y-1/2 before:w-px before:h-3.5 before:bg-gray-400 before:rotate-[18deg]">
                                    <a class="hover:text-indigo-600 focus:outline-hidden focus:underline transition-colors" href="/privacy">
                                        Privacy
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </footer>

            </div>
        </div>
    </div>

    <!-- Dashboard JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("adminSidebar");
            const backdrop = document.getElementById("mobileBackdrop");
            const openBtn = document.getElementById("openSidebar");
            
            function updateHamburgerIcon(isOpen) {
                const hamburgerIcon = openBtn && openBtn.querySelector("svg");
                if (hamburgerIcon) {
                    if (isOpen) {
                        hamburgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                    } else {
                        hamburgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    }
                }
            }
            
            function openSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.add("mobile-open");
                    backdrop.classList.add("show");
                    document.body.style.overflow = "hidden";
                    updateHamburgerIcon(true);
                }
            }
            
            function closeSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.remove("mobile-open");
                    backdrop.classList.remove("show");
                    document.body.style.overflow = "";
                    updateHamburgerIcon(false);
                }
            }
            
            function toggleSidebar() {
                const isCurrentlyOpen = sidebar && sidebar.classList.contains("mobile-open");
                if (isCurrentlyOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
            
            if (openBtn) {
                openBtn.addEventListener("click", toggleSidebar);
            }
            
            if (backdrop) {
                backdrop.addEventListener("click", closeSidebar);
            }
            
            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape") {
                    closeSidebar();
                }
            });
        });
    </script>

    <script src="/assets/js/app.js?v=<?php echo filemtime(MVCLM_ROOT . '/assets/js/app.js'); ?>"></script>
</body>
</html>