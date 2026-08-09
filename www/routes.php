<?php
/**
 * The routing table. Required by index.php with $router already constructed.
 *
 * The routes are evaluated in order, and the first match wins.
 * Since the profile route is at the bottom, it accepts any single word as username.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

// Public routes
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('about', ['controller' => 'AboutController', 'action' => 'index']);
$router->add('contact', ['controller' => 'ContactController', 'action' => 'index']);
$router->add('terms', ['controller' => 'TermsController', 'action' => 'index']);
$router->add('privacy', ['controller' => 'PrivacyController', 'action' => 'index']);

// Authentication routes
$router->add('login', ['controller' => 'LoginController', 'action' => 'index']);
$router->add('logout', ['controller' => 'LoginController', 'action' => 'logout']);
$router->add('register', ['controller' => 'RegisterController', 'action' => 'index']);

// Email verification routes
$router->add('verify-email/{token:[a-f0-9]+}', ['controller' => 'VerifyEmailController', 'action' => 'index']);
$router->add('resend-verification', ['controller' => 'VerifyEmailController', 'action' => 'resend']);

$router->add('forgot-password', ['controller' => 'PasswordResetController', 'action' => 'index']);
$router->add('reset-password/{token:[a-f0-9]+}', ['controller' => 'PasswordResetController', 'action' => 'reset']);

// AJAX routes
$router->add('ajax-username', ['controller' => 'AjaxController', 'action' => 'username']);

// Authenticated user routes
$router->add('dashboard', ['controller' => 'DashboardController', 'action' => 'index']);
$router->add('settings', ['controller' => 'SettingsController', 'action' => 'index']);
$router->add('delete/{uid:\d+}', ['controller' => 'ProfileController', 'action' => 'delete']);

// Admin routes
$router->add('users', ['controller' => 'UsersController', 'action' => 'index']);
$router->add('users/page-{page:\d+}', ['controller' => 'UsersController', 'action' => 'index']);
$router->add('user-management/{uid:\d+}', ['controller' => 'UserManagementController', 'action' => 'edit']);
$router->add('analytics', ['controller' => 'AnalyticsController', 'action' => 'index']);
$router->add('app-settings', ['controller' => 'AppSettingsController', 'action' => 'index']);

// User profile route - MUST BE LAST
$router->add('{username:\w+}', ['controller' => 'ProfileController', 'action' => 'index']);

