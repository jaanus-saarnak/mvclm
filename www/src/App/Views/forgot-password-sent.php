<?php
/**
 * Shown after an address is submitted, whether or not it belongs to an account
 *
 * The wording is deliberately conditional. Saying a link was sent would answer
 * a question this page must not answer.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Check Your Email - ' . CONFIG['app_name'];
$description = 'Open the link we sent to choose a new ' . CONFIG['app_name'] . ' password.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-md px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="text-center">

            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50">
                <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
            </div>

            <h1 class="mt-6 font-medium text-xl text-gray-800">
                Check your email
            </h1>

            <p class="mt-3 text-gray-600">
                If there is an account for
                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($email, ENT_QUOTES); ?></span>,
                a link to choose a new password is on its way.
            </p>

            <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 text-left">
                <h2 class="text-sm font-medium text-gray-800">
                    If it does not arrive
                </h2>
                <ul class="mt-2 space-y-1 text-sm text-gray-600 list-disc list-inside">
                    <li>Give it a minute, then look in your spam folder.</li>
                    <li>Check the address you typed is the one you signed up with.</li>
                    <li>The link works once and expires in <?php echo \App\PasswordReset::lifetimePhrase(); ?>.</li>
                </ul>
            </div>

            <a href="<?php echo CONFIG['url']; ?>/login"
               class="mt-8 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Go to sign in
            </a>

        </div>
    </div>
</main>
