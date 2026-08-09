<?php
/**
 * Where a visitor asks for a link to choose a new password
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Forgot Password - ' . CONFIG['app_name'];
$description = 'Ask for a link to choose a new ' . CONFIG['app_name'] . ' password.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-xs px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="space-y-8">
            <div class="text-center">
                <h1 class="font-medium text-xl text-gray-800">
                    Forgot your password?
                </h1>
                <p class="mt-3 text-sm text-gray-600">
                    Enter the address you signed up with and we will send you a link to choose a new one.
                </p>
            </div>

            <form method="post" action="<?php echo CONFIG['url']; ?>/forgot-password">
                <?php echo \Core\Csrf::field(); ?>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input name="email" id="email" type="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Enter your email"
                        required
                        autocomplete="email"
                        autofocus>
                </div>

                <div class="space-y-4 mt-6">
                    <button type="submit"
                        class="py-3 px-4 w-full inline-flex justify-center items-center gap-x-2 sm:text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-indigo-700 transition-colors duration-200">
                        Send me a link
                    </button>

                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Remembered it?<br>
                            <a href="<?php echo CONFIG['url']; ?>/login" class="text-indigo-600 hover:text-indigo-500 font-medium underline transition-colors">
                                Back to sign in
                            </a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
