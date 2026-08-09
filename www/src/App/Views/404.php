<?php
/**
 * Not found page
 *
 * Rendered by the router when no route matches, and by ProfileController to
 * hide a profile the visitor may not see.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

$title = '404 Page Not Found - ' . CONFIG['app_name'];
$description = 'The page you are looking for could not be found. It may have been moved or deleted.';
?>
<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center">
            <p class="text-6xl font-bold text-indigo-600 mb-4">404</p>

            <h1 class="text-2xl font-bold text-gray-900 mb-4">Page Not Found</h1>
            <p class="text-gray-600 mb-8">
                Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo CONFIG['url']; ?>" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go to Homepage
                </a>
                
                <button onclick="history.back()" 
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Go Back
                </button>
            </div>
        </div>
    </div>
</main>