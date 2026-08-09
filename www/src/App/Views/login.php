<?php
/**
 * Sign-in page
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Sign In - ' . CONFIG['app_name'];
$description = 'Sign in to your ' . CONFIG['app_name'] . ' account to access your dashboard and manage your profile.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-xs px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="">
            <div class="space-y-8">
                <div class="text-center">
                    <h1 class="font-medium text-xl text-gray-800">
                        Sign In to <?php echo htmlspecialchars(CONFIG['app_name']); ?>
                    </h1>
                </div>

                <form method="post" action="<?php echo CONFIG['url']; ?>/login" id="loginForm">
                    <?php echo \Core\Csrf::field(); ?>
                    <div class="space-y-3">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input name="username" id="username" type="text" 
                                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none" 
                                placeholder="Enter your username"
                                required 
                                autocomplete="username"
                                autofocus>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input name="password" id="password" type="password" 
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none" 
                                    placeholder="Enter your password" 
                                    required 
                                    autocomplete="current-password">
                                    
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-between items-center gap-3">
                            <div class="flex gap-x-1">
                                <input type="checkbox" 
                                    class="shrink-0 border-gray-300 size-[18px] rounded-sm text-indigo-600 focus:ring-indigo-600 disabled:opacity-50 disabled:pointer-events-none" 
                                    id="remember" name="remember">
                                <label for="remember" class="text-[13px] text-gray-500 ms-1.5">
                                    Remember me
                                </label>
                            </div>

                            <?php if (\App\PasswordReset::isEnabled()) : ?>
                                <a class="text-[13px] text-indigo-600 decoration-2 hover:underline font-medium" href="<?php echo CONFIG['url']; ?>/forgot-password">
                                    Forgot password?
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-4 mt-6">
                        <button type="submit" id="submitBtn" 
                            class="py-3 px-4 w-full inline-flex justify-center items-center gap-x-2 sm:text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-indigo-700 transition-colors duration-200">
                            <span id="submitText">Sign In</span>
                            <span id="submitSpinner" class="hidden">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>

                        <div class="text-center pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                Don't have an account?<br>
                                <a href="<?php echo CONFIG['url']; ?>/register" class="text-indigo-600 hover:text-indigo-500 font-medium underline transition-colors">
                                    Create one here
                                </a>
                            </p>
                        </div>

                        <?php if (CONFIG['demo_mode']) { ?>
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-5">
                                <p class="text-gray-700 leading-relaxed">
                                    Username: admin<br>
                                    Password: KHm2pX7MbZ
                                </p>
                            </div>
                        <?php } ?>

                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const form = document.getElementById('loginForm');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'text') {
            this.innerHTML = `
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.757 6.757M9.878 9.878a3 3 0 00.007 4.243m4.242-4.242L15.14 7.758M14.121 14.121l2.121 2.122"></path>
                </svg>
            `;
        } else {
            this.innerHTML = `
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            `;
        }
    });

    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitSpinner.classList.remove('hidden');
    });
});
</script>

