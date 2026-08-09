<?php
/**
 * Settings template.
 * Copy this file to env.php and fill it in.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

define("CONFIG", [
    'app_name' => 'MVCLM Boilerplate', // Shown in the page title and the header
    'url' => 'http://example.com', // App URL with no slash '/' at the end
    'db_host' => 'localhost', // Usually 'localhost'
    'db_name' => '', // Database name
    'db_user' => '', // Database user
    'db_password' => '', // Database password
    'wrong_passwords_limit_from_ip' => 100, // Failed sign-ins from one IP address before it is blocked
    'require_login_to_view_profile' => true, // Hide profile pages from visitors who are not signed in
    'show_errors' => false, // Display errors as well as recording them. Set to false on a live site
    'demo_mode' => false, // Block visitors registering, deleting accounts and changing access levels
    'require_email_verification' => false, // Make a new account confirm its Email address before it can sign in. Needs the SMTP settings below
    'enable_password_reset' => false, // Offer a 'Forgot password?' link that mails a reset link. Needs the SMTP settings below
    'smtp_host' => '', // SMTP server hostname
    'smtp_port' => 587, // 587 or 2525 with 'tls', 465 with 'ssl'
    'smtp_username' => '', // SMTP username
    'smtp_password' => '', // SMTP password
    'smtp_encryption' => 'tls', // 'tls' to upgrade with STARTTLS, 'ssl' to connect encrypted, '' for neither
    'mail_from_address' => '', // The email address outgoing mail is sent from
    'mail_from_name' => '', // The name shown beside email, optional
    'contact_to_address' => '', // Where the contact form sends the email. Empty hides the form
    'path' => 'src' // Change this only if you rename or move the src folder
]);
