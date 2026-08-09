<?php
/**
 * Error page, shown when show_errors is off and something has thrown
 *
 * A complete HTML document, rendered without a layout. Keep it free of
 * application data: whatever failed may be the config or the database.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */
?><!DOCTYPE html>
<html lang="en" class="relative min-h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Internal Server Error</title>
    <meta name="description" content="An unexpected error occurred. Our team has been notified and is working to resolve the issue.">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Link the stylesheet as a plain static path. No PHP call, no cache-bust. -->
    <link rel="stylesheet" href="/assets/css/app.css">

    <!-- Fallback: if the stylesheet is missing the text still reads. -->
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <main class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Internal Server Error</h1>
            <p class="text-gray-600 mb-8">
                We're sorry, but something went wrong on our end. Our team has been notified and is working to fix the issue.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="history.back()" 
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Go Back
                </button>
                
                <button onclick="location.reload()" 
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Try Again
                </button>
            </div>
            
            <div class="mt-12 bg-red-50 rounded-lg p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">What happened?</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>An unexpected error occurred while processing your request. This is usually temporary and should be resolved soon.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-xs text-gray-500">
                Error occurred at: <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
    </main>
</body>
</html>