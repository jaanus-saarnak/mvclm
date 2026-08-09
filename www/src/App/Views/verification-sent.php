<?php
/**
 * Shown after signing up, when the email address still has to be confirmed
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = ($sent ? 'Check Your Email' : 'Account Created') . ' - ' . CONFIG['app_name'];
$description = 'Confirm your email address to finish setting up your ' . CONFIG['app_name'] . ' account.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-md px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="text-center">

            <?php if ($sent) : ?>

                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50">
                    <svg class="w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>

                <h1 class="mt-6 font-medium text-xl text-gray-800">
                    Check your email
                </h1>

                <p class="mt-3 text-gray-600">
                    We sent a confirmation link to
                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($email, ENT_QUOTES); ?></span>.
                    Open it to finish setting up your account.
                </p>

            <?php else : ?>

                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-amber-50">
                    <svg class="w-8 h-8 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L9.4 3.751c.866-1.5 2.998-1.5 3.864 0l7.14 12.375ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>

                <h1 class="mt-6 font-medium text-xl text-gray-800">
                    Account created
                </h1>

                <p class="mt-3 text-gray-600">
                    We could not send the confirmation link to
                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($email, ENT_QUOTES); ?></span>
                    just now. Your account is saved. Sign in and we will try again.
                </p>

            <?php endif; ?>

            <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 text-left">
                <h2 class="text-sm font-medium text-gray-800">
                    <?php echo $sent ? 'If it does not arrive' : 'What happens next'; ?>
                </h2>
                <ul class="mt-2 space-y-1 text-sm text-gray-600 list-disc list-inside">
                    <?php if ($sent) : ?>
                        <li>Give it a minute, then look in your spam folder.</li>
                        <li>The link works once and expires in <?php echo \App\Models\EmailVerificationModel::LIFETIME_HOURS; ?> hours.</li>
                    <?php endif; ?>
                    <li>Sign in with your new account to have another link sent.</li>
                </ul>
            </div>

            <a href="<?php echo CONFIG['url']; ?>/login"
               class="mt-8 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Go to sign in
            </a>

        </div>
    </div>
</main>
