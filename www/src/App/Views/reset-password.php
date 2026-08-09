<?php
/**
 * Where a working reset link lets someone choose a new password
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Choose a New Password - ' . CONFIG['app_name'];
$description = 'Choose a new password for your ' . CONFIG['app_name'] . ' account.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-xs px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="space-y-8">
            <div class="text-center">
                <h1 class="font-medium text-xl text-gray-800">
                    Choose a new password
                </h1>
                <p class="mt-3 text-sm text-gray-600">
                    Pick something you have not used here before. You will sign in with it straight away.
                </p>
            </div>

            <form method="post" action="<?php echo CONFIG['url']; ?>/reset-password/<?php echo htmlspecialchars($token, ENT_QUOTES); ?>">
                <?php echo \Core\Csrf::field(); ?>
                <div class="space-y-3">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                        <input name="password" id="password" type="password"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none"
                            placeholder="6 to 20 characters"
                            required
                            autocomplete="new-password"
                            autofocus>
                    </div>

                    <div>
                        <label for="password-again" class="block text-sm font-medium text-gray-700 mb-1">New password again</label>
                        <input name="password-again" id="password-again" type="password"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none"
                            placeholder="Type it once more"
                            required
                            autocomplete="new-password">
                    </div>
                </div>

                <div class="space-y-4 mt-6">
                    <button type="submit"
                        class="py-3 px-4 w-full inline-flex justify-center items-center gap-x-2 sm:text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-indigo-700 transition-colors duration-200">
                        Save new password
                    </button>

                    <p class="text-center text-sm text-gray-500">
                        This link works once and expires in <?php echo \App\PasswordReset::lifetimePhrase(); ?>.
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>
