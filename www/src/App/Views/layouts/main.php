<?php
/**
 * Public layout, used by every view that does not ask for another
 *
 * Flash messages are rendered here, so no page renders its own.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */
?><!DOCTYPE html>
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
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="flex flex-wrap md:justify-start md:flex-nowrap z-50 bg-white border-b border-gray-200 h-16 relative">
        <div class="max-w-5xl mx-auto px-2 sm:px-5 xl:px-0 flex flex-wrap basis-full items-center w-full h-full">
            <div class="lg:order-1 flex items-center h-full">
                <a class="flex-none rounded-md text-2xl inline-block font-semibold focus:outline-hidden focus:opacity-80 text-indigo-600" href="/" aria-label="<?php echo htmlspecialchars(CONFIG['app_name']); ?>">
                    <?php echo htmlspecialchars(CONFIG['app_name']); ?>
                </a>
            </div>
            
            <div class="lg:order-4 flex justify-end items-center gap-x-1 sm:gap-x-1 ms-auto lg:ms-0 h-full">
                <?php
                if (USER_LOGGED) {
                    ?>
                    <a href="/dashboard" type="button"
                        class="mr-2 py-1.5 px-3 inline-flex items-center gap-x-2 text-base rounded-lg bg-gray-100 text-gray-800 shadow-xs hover:text-indigo-600 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-200">
                        Dashboard
                    </a>

                    <div class="hs-dropdown [--strategy:absolute] [--auto-close:inside] [--placement:bottom-right] relative inline-flex">
                        <button id="hs-pro-dnad" type="button" 
                            class="p-1.5 inline-flex items-center gap-x-2 text-base rounded-lg text-gray-800 hover:bg-gray-50 hover:text-indigo-600 focus:bg-gray-50" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
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

                        <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-60 transition-[opacity,margin] duration opacity-0 hidden z-20 bg-white rounded-xl shadow-[0_10px_40px_10px_rgba(0,0,0,0.08)] absolute top-full right-0 mt-2" role="menu" aria-orientation="vertical" aria-labelledby="hs-pro-dnad">
                            <div class="p-1">
                                <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-base text-gray-800 hover:bg-gray-50 hover:text-indigo-600 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden" href="/<?php echo LOGGED_USER_PROFILE['username']; ?>">
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
                                    Settings
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
                    <?php
                } else {
                    ?>
                    <a href="/login" type="button" class="py-1.5 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100">
                        Log in
                    </a>
                    <a href="/register" type="button" class="mx-2 py-1.5 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                        Register
                    </a>
                    <?php
                }   ?>

                <div class="lg:hidden">  
                    <button type="button" class="hs-collapse-toggle inline-flex justify-center items-center w-7 h-8 text-start border border-gray-200 text-gray-800 rounded-lg shadow-xs align-middle disabled:opacity-50 focus:outline-hidden focus:bg-gray-100 hover:bg-gray-100" id="hs-pro-dmh-collapse" aria-expanded="false" aria-controls="hs-pro-dmh" aria-label="Toggle navigation" data-hs-collapse="#hs-pro-dmh">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="1" /><circle cx="12" cy="5" r="1" /><circle cx="12" cy="19" r="1" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Nav Links -->
            <div class="lg:order-2 basis-full grow lg:basis-auto lg:ms-5 h-full lg:flex">
                <div id="hs-pro-dmh" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow lg:!flex lg:h-full lg:justify-center lg:items-center absolute lg:relative top-full lg:top-auto left-0 lg:left-auto right-0 lg:right-auto bg-white lg:bg-transparent shadow-lg lg:shadow-none border-t lg:border-t-0 border-gray-200 z-50 lg:z-auto" aria-labelledby="hs-pro-dmh-collapse">
                    <div class="overflow-hidden overflow-y-auto max-h-[75vh] lg:max-h-none lg:h-full lg:flex lg:items-center [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                        <div class="flex flex-col lg:flex-row lg:justify-center lg:items-center gap-1 lg:gap-2 p-4 lg:p-0 pt-2 lg:pt-0">                    
                            <?php
                                $mainMenuLinks = implode(' ', [
                                    'p-2',
                                    'inline-flex',
                                    'items-center',
                                    'text-base',
                                    'text-gray-800',
                                    'hover:bg-gray-50',
                                    'hover:text-indigo-600',
                                    'rounded-lg',
                                    'focus:outline-hidden'
                                ]);
                            ?>
                            
                            <a href="/" class="<?php echo $mainMenuLinks; ?>">
                                <svg class="shrink-0 size-4 me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Home
                            </a>

                            <a href="/about" class="<?php echo $mainMenuLinks; ?>">
                                <svg class="shrink-0 size-4 me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                About
                            </a>
							
                            <a href="/contact" class="<?php echo $mainMenuLinks; ?>">
                                <svg class="shrink-0 size-4 me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

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

    <!-- Main Content -->
    <?php echo $content; ?>

    <!-- Footer -->
    <footer class="h-[40px] sm:h-[64px] absolute bottom-0 inset-x-0">
        <div class="max-w-5xl p-2 sm:px-5 xl:px-0 sm:py-5 mx-auto">
            <div class="flex justify-between items-center">
                <p class="text-xs sm:text-sm text-gray-500">
                    MIT License
                </p>

                <ul>
                    <li class="inline-block relative pe-5 text-xs sm:text-sm text-gray-500 align-middle last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-2 before:-translate-y-1/2 before:w-px before:h-3.5 before:bg-gray-400 before:rotate-[18deg]">
                        <a class="hover:text-indigo-600 focus:outline-hidden focus:underline" href="/terms">
                            Terms
                        </a>
                    </li>
                    <li class="inline-block relative pe-5 text-xs sm:text-sm text-gray-500 align-middle last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-2 before:-translate-y-1/2 before:w-px before:h-3.5 before:bg-gray-400 before:rotate-[18deg]">
                        <a class="hover:text-indigo-600 focus:outline-hidden focus:underline" href="/privacy">
                            Privacy
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </footer>

    <!-- Mobile Menu -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('hs-pro-dmh');
            const mobileMenuToggle = document.getElementById('hs-pro-dmh-collapse');
            
            document.addEventListener('click', function(event) {
                if (window.innerWidth >= 1024) return;
                
                if (!mobileMenu.classList.contains('hidden')) {
                    if (!mobileMenu.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                        mobileMenuToggle.click();
                    }
                }
            });
        });
    </script>

    <script src="/assets/js/app.js?v=<?php echo filemtime(MVCLM_ROOT . '/assets/js/app.js'); ?>"></script>
</body>
</html>