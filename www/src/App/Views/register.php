<?php
/**
 * Registration form
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Create Account - ' . CONFIG['app_name'];
$description = 'Join ' . CONFIG['app_name'] . ' today. Create your free account and start managing your profile.';
?>

<main id="content" class="pb-[92px] sm:pb-[64px]">
    <div class="py-10 lg:py-20 w-full max-w-sm px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="">
            <div class="space-y-8">
                <div class="text-center">
                    <h1 class="font-medium text-xl text-gray-800">
                        Create Your Account
                    </h1>
                </div>

                <form method="post" action="<?php echo CONFIG['url']; ?>/register" id="registerForm" onkeyup="checkThatUsernameIsNotTaken();" novalidate>
                    <?php echo \Core\Csrf::field(); ?>

                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input 
                                name="username" 
                                id="username" 
                                type="text" 
                                value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none" 
                                placeholder="Choose a username" 
                                required
                                autocomplete="username"
                                pattern="[a-zA-Z0-9]{3,16}"
                                minlength="3"
                                maxlength="16">
                            <p class="mt-1 text-xs text-gray-500">3-16 characters, letters and numbers only</p>
                            <div id="username-status" class="text-red-500 text-xs mt-1"></div>
                            <div class="text-red-500 text-xs mt-1 hidden" id="username_error"></div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input 
                                name="email" 
                                id="email" 
                                type="email" 
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none" 
                                placeholder="Enter your email address" 
                                required
                                autocomplete="email">
                            <div class="text-red-500 text-xs mt-1 hidden" id="email_error"></div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    name="password" 
                                    id="password" 
                                    type="password" 
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none" 
                                    placeholder="Create a password" 
                                    required
                                    autocomplete="new-password"
                                    minlength="6"
                                    maxlength="20">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div id="passwordStrength" class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <span id="passwordStrengthText" class="text-xs text-gray-500">Weak</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">6-20 characters required</p>
                            </div>
                            <div class="text-red-500 text-xs mt-1 hidden" id="password_error"></div>
                        </div>

                        <div>
                            <label for="password-again" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input 
                                name="password-again" 
                                id="password-again" 
                                type="password" 
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none" 
                                placeholder="Confirm your password" 
                                required
                                autocomplete="new-password"
                                minlength="6"
                                maxlength="20">
                            <div class="text-red-500 text-xs mt-1 hidden" id="password_again_error"></div>
                            <div id="passwordMatch" class="mt-1 text-xs hidden">
                                <span class="text-red-500">Passwords do not match</span>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    id="terms" 
                                    name="terms" 
                                    type="checkbox" 
                                    value="1"
                                    <?php echo (isset($_POST['terms']) && $_POST['terms']) ? 'checked' : ''; ?>
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded-sm">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-700">
                                    I agree to the 
                                    <a href="<?php echo CONFIG['url']; ?>/terms" class="text-indigo-600 hover:text-indigo-500 underline" target="_blank">Terms of Service</a> 
                                    and 
                                    <a href="<?php echo CONFIG['url']; ?>/privacy" class="text-indigo-600 hover:text-indigo-500 underline" target="_blank">Privacy Policy</a>
                                </label>
                                <div id="termsError" class="mt-1 text-xs hidden">
                                    <span class="text-red-500">Please accept the Terms of Service and Privacy Policy to continue</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mt-6">
                        <button
                            type="submit"
                            id="submitBtn"
                            class="py-3 px-4 w-full inline-flex justify-center items-center gap-x-2 sm:text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-indigo-700 transition-colors duration-200">
                            <span id="submitText">Create Account</span>
                            <span id="submitSpinner" class="hidden">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>

                        <div class="text-center pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                Already have an account? 
                                <a href="<?php echo CONFIG['url']; ?>/login" class="text-indigo-600 hover:text-indigo-500 font-medium underline transition-colors">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function checkThatUsernameIsNotTaken() {
    let request;
    let message = document.getElementById('username-status');

    request = $.ajax({
        url: "<?php echo CONFIG['url']; ?>/ajax-username",
        type: "post",
        // Send the token with every post. A script reads it from the meta element.
        data: {
            username: $('#username').val(),
            csrf_token: window.MVCLM.csrfToken()
        }
    });

    request.done(function (response) {
        message.innerHTML = response;

        if (response) {
            message.style.marginBottom = '-16px';
        } else {
            message.style.marginBottom = '0px';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirm = document.getElementById('password-again');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordStrengthText = document.getElementById('passwordStrengthText');
    const passwordMatch = document.getElementById('passwordMatch');
    const togglePassword = document.getElementById('togglePassword');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const form = document.getElementById('registerForm');
    const usernameInput = document.getElementById('username');

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

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let strengthText = 'Weak';
        let strengthColor = 'bg-red-500';

        if (password.length >= 6) strength += 25;
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]/)) strength += 12.5;
        if (password.match(/[A-Z]/)) strength += 12.5;
        if (password.match(/[0-9]/)) strength += 12.5;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 12.5;

        if (strength >= 75) {
            strengthText = 'Strong';
            strengthColor = 'bg-green-500';
        } else if (strength >= 50) {
            strengthText = 'Medium';
            strengthColor = 'bg-yellow-500';
        } else if (strength >= 25) {
            strengthText = 'Fair';
            strengthColor = 'bg-orange-500';
        }

        passwordStrength.style.width = strength + '%';
        passwordStrength.className = `h-2 rounded-full transition-all duration-300 ${strengthColor}`;
        passwordStrengthText.textContent = strengthText;
        passwordStrengthText.className = `text-xs ${strengthColor.replace('bg-', 'text-')}`;
    });

    function checkPasswordMatch() {
        if (passwordConfirm.value && passwordInput.value !== passwordConfirm.value) {
            passwordMatch.classList.remove('hidden');
            passwordConfirm.classList.add('border-red-300');
            return false;
        } else {
            passwordMatch.classList.add('hidden');
            passwordConfirm.classList.remove('border-red-300');
            return true;
        }
    }

    passwordConfirm.addEventListener('input', checkPasswordMatch);
    passwordInput.addEventListener('input', checkPasswordMatch);

    usernameInput.addEventListener('input', function() {
        let value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '');
        this.value = value;
        
        if (value.length < 3 && value.length > 0) {
            this.classList.add('border-red-300');
        } else {
            this.classList.remove('border-red-300');
        }
    });

    // The form carries novalidate, so this list is what makes required true in
    // the browser. Do not trim passwords: a space is a legitimate character.
    const requiredFields = [
        {input: usernameInput, error: document.getElementById('username_error'), message: 'Choose a username', trim: true},
        {input: document.getElementById('email'), error: document.getElementById('email_error'), message: 'Enter your email address', trim: true},
        {input: passwordInput, error: document.getElementById('password_error'), message: 'Create a password', trim: false},
        {input: passwordConfirm, error: document.getElementById('password_again_error'), message: 'Confirm your password', trim: false}
    ];

    function showFieldError(field) {
        field.error.textContent = field.message;
        field.error.classList.remove('hidden');
        field.input.classList.add('border-red-300');
    }

    function clearFieldError(field) {
        field.error.textContent = '';
        field.error.classList.add('hidden');
        field.input.classList.remove('border-red-300');
    }

    // Clear a field's message as soon as it has something in it.
    requiredFields.forEach(function(field) {
        field.input.addEventListener('input', function() {
            if (field.input.value !== '') {
                clearFieldError(field);
            }
        });
    });

    form.addEventListener('submit', function(e) {
        const termsCheckbox = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        let firstInvalid = null;

        // Run every check before returning, so a visitor sees all the problems
        // at once. Use the field's own inline element, never a dialog.
        requiredFields.forEach(function(field) {
            clearFieldError(field);

            const value = field.trim ? field.input.value.trim() : field.input.value;
            if (value === '') {
                showFieldError(field);
                if (firstInvalid === null) {
                    firstInvalid = field.input;
                }
            }
        });

        // Compared directly, not through checkPasswordMatch(), which ignores an
        // empty confirmation on purpose while the field is still being typed in.
        const bothEntered = passwordInput.value !== '' && passwordConfirm.value !== '';
        if (bothEntered && passwordInput.value !== passwordConfirm.value) {
            passwordMatch.classList.remove('hidden');
            passwordConfirm.classList.add('border-red-300');
            if (firstInvalid === null) {
                firstInvalid = passwordConfirm;
            }
        } else {
            passwordMatch.classList.add('hidden');
        }

        termsError.classList.toggle('hidden', termsCheckbox.checked);
        if (!termsCheckbox.checked && firstInvalid === null) {
            firstInvalid = termsCheckbox;
        }

        if (firstInvalid !== null) {
            e.preventDefault();
            firstInvalid.focus();
            return;
        }

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitSpinner.classList.remove('hidden');
    });
});
</script>

