<?php
/**
 * Shown when the password was right but the email address is not confirmed yet
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Confirm Your Email - ' . CONFIG['app_name'];
$description = 'Confirm your email address to sign in to ' . CONFIG['app_name'] . '.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-md px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="text-center">

            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-amber-50">
                <svg class="w-8 h-8 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>

            <h1 class="mt-6 font-medium text-xl text-gray-800">
                Confirm your email first
            </h1>

            <p class="mt-3 text-gray-600">
                Your password was correct, but
                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($email, ENT_QUOTES); ?></span>
                has not been confirmed yet. Open the link we sent you and then sign in.
            </p>

            <form method="post" action="<?php echo CONFIG['url']; ?>/resend-verification" class="mt-8">
                <?php echo \Core\Csrf::field(); ?>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Send me a new link
                </button>
            </form>

            <p class="mt-4 text-sm text-gray-500">
                Links last <?php echo \App\Models\EmailVerificationModel::LIFETIME_HOURS; ?> hours, and a new one replaces the old.
            </p>

        </div>
    </div>
</main>
