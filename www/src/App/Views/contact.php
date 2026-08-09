<?php
/**
 * Contact page
 *
 * The form appears only when $formIsAvailable, which the controller answers
 * from contact_to_address in env.php.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

$title = 'Contact Us - ' . CONFIG['app_name'];
$description = 'Get in touch with ' . CONFIG['app_name'] . '. We are here to help and answer any questions you may have.';
?>

<main id="content" class="mb-5">
    <div class="md:max-w-screen-sm text-center px-4 sm:px-6 lg:px-8 pt-10 pb-6 mx-auto">
        <h1 class="mb-4 text-2xl font-bold md:text-3xl text-gray-900">Contact Us</h1>

        <?php if ($formIsAvailable) { ?>
            <p class="mt-2 text-gray-600">Send us a message and we will get back to you.</p>
        <?php } else { ?>
            <p class="mt-2 text-gray-600">The contact form is not available at the moment.</p>
        <?php } ?>
    </div>

    <?php if ($formIsAvailable) { ?>
        <div class="w-full max-w-lg px-4 sm:px-6 lg:px-8 pb-16 mx-auto">
            <form method="post" action="<?php echo CONFIG['url']; ?>/contact"
                class="bg-white shadow-xs rounded-lg border border-gray-200 p-6 sm:p-8">
                <?php echo \Core\Csrf::field(); ?>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input name="name" id="name" type="text"
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Your name"
                        maxlength="<?php echo \App\Contact::NAME_MAX; ?>"
                        required
                        autocomplete="name"
                        autofocus>
                </div>

                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input name="email" id="email" type="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Your email"
                        required
                        autocomplete="email">
                </div>

                <div class="mt-4">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" id="message" rows="7"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="How can we help?"
                        maxlength="<?php echo \App\Contact::MESSAGE_MAX; ?>"
                        required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>

                <?php /* Hidden from people and left for scripts to fill in. Not required,
                         out of the tab order, and told not to autofill, so nothing a real
                         visitor's browser does on their behalf can put a value here. */ ?>
                <div class="hidden" aria-hidden="true">
                    <label for="website">Website</label>
                    <input name="website" id="website" type="text" value="" tabindex="-1" autocomplete="off">
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="py-3 px-4 w-full inline-flex justify-center items-center gap-x-2 sm:text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-indigo-700 transition-colors duration-200">
                        Send message
                    </button>
                </div>
            </form>
        </div>
    <?php } ?>
</main>
