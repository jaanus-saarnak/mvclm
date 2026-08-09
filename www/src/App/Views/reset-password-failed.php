<?php
/**
 * Why a reset link did not open a form
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$outcomes = [
    'used' => [
        'title' => 'This link has been used',
        'body' => 'A password was already chosen with it. Sign in with the new one, or ask for another link.',
        'tone' => 'gray',
        'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    ],
    'expired' => [
        'title' => 'This link has expired',
        'body' => 'Reset links last ' . \App\PasswordReset::lifetimePhrase() . '.',
        'tone' => 'amber',
        'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    ],
    'unknown' => [
        'title' => 'We do not recognise this link',
        'body' => 'It may have been replaced by a newer one, or copied incompletely from your email.',
        'tone' => 'gray',
        'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
    ],
];

$outcome = $outcomes[$state] ?? $outcomes['unknown'];

$title = $outcome['title'] . ' - ' . CONFIG['app_name'];
$description = 'Resetting your ' . CONFIG['app_name'] . ' password.';
$tones = [
    'amber' => ['bg-amber-50', 'text-amber-600'],
    'gray' => ['bg-gray-100', 'text-gray-500'],
];
[$badge, $stroke] = $tones[$outcome['tone']];
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-md px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="text-center">

            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full <?php echo $badge; ?>">
                <svg class="w-8 h-8 <?php echo $stroke; ?>" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $outcome['icon']; ?>" />
                </svg>
            </div>

            <h1 class="mt-6 font-medium text-xl text-gray-800">
                <?php echo htmlspecialchars($outcome['title'], ENT_QUOTES); ?>
            </h1>

            <p class="mt-3 text-gray-600">
                <?php echo htmlspecialchars($outcome['body'], ENT_QUOTES); ?>
            </p>

            <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 text-left">
                <h2 class="text-sm font-medium text-gray-800">Getting a new link</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Ask for another from the sign-in page. A new link replaces whatever came before it.
                </p>
            </div>

            <a href="<?php echo CONFIG['url']; ?>/forgot-password"
               class="mt-8 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Ask for a new link
            </a>

        </div>
    </div>
</main>
