<?php
/**
 * Front controller. Every request that is not a real file arrives here, sent by
 * .htaccess, which passes the path along as the query string. That is why the
 * router is handed QUERY_STRING at the bottom.
 *
 * Every POST is checked for a CSRF token here rather than in the controllers, so
 * a form added later is covered without its author knowing this exists. Do not
 * add a check of your own to a controller.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

use App\AccountStatus;
use App\Controllers\LoginController;
use App\Flash;
use App\Models\UsersModel;
use Core\Controller;
use Core\Csrf;
use Core\Router;

try {
    require_once __DIR__ . '/../env.php';
} catch (\Throwable $e) {
    throw new RuntimeException("Failed to load configuration: " . $e->getMessage(), 0, $e);
}

define("MVCLM_VERSION", "2.0");
define("MVCLM_ROOT", __DIR__);

try {
    require_once CONFIG['path'] . '/vendor/autoload.php';
} catch (\Throwable $e) {
    throw new RuntimeException("Failed to load Composer autoloader: " . $e->getMessage(), 0, $e);
}

error_reporting(E_ALL);
set_error_handler(['Core\Error', 'errorHandler']);
set_exception_handler(['Core\Error', 'exceptionHandler']);

try {
    session_start();
} catch (\Throwable $e) {
    throw new RuntimeException("Failed to start session: " . $e->getMessage(), 0, $e);
}

/*
 * CSRF gate.
 *
 * A rejected form goes back to the page it came from with a flash, the same as
 * every other refusal here. The form is re-rendered with a fresh token, so a
 * visitor whose session simply expired can try again.
 *
 * Build that address from CONFIG['url'], never from REQUEST_URI as it arrives:
 * a path starting with two slashes would send the visitor to another site.
 *
 * An XHR gets 403 and one line of text, because it cannot follow a redirect.
 */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !Csrf::verify($_POST[Csrf::FIELD_NAME] ?? null)) {
    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Your session has expired. Please reload the page.';
        exit;
    }

    Flash::addMessage('That form has expired. Please try again.', Flash::DANGER);

    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    Controller::redirect(CONFIG['url'] . '/' . ltrim(is_string($requestPath) ? $requestPath : '', '/'));
}

if (isset($_COOKIE['uid']) && isset($_COOKIE['hash'])) {
    try {
        LoginController::loginFromCookie(intval($_COOKIE['uid']), $_COOKIE['hash']);
    } catch (\Throwable $e) {
        throw new RuntimeException("Cookie-based login failed: " . $e->getMessage(), 0, $e);
    }
}

try {
    $loggedUid = LoginController::userLogged();
} catch (\Throwable $e) {
    throw new RuntimeException("Failed to determine login status: " . $e->getMessage(), 0, $e);
}

if ($loggedUid) {
    define("USER_LOGGED", true);
    try {
        $userProfile = UsersModel::getByID($loggedUid);
        define("LOGGED_USER_PROFILE", $userProfile);
    } catch (\Throwable $e) {
        throw new RuntimeException("Failed to retrieve logged user's profile: " . $e->getMessage(), 0, $e);
    }

    // An administrator can change a status while its owner is already inside, so
    // this is asked on every request rather than only at the login form. It is
    // also what covers the remembered-cookie sign-in above, which hands out a
    // session without the login form running at all.
    $accountStatus = (string) ($userProfile['status'] ?? '');

    if (AccountStatus::isBlocked($accountStatus)) {
        LoginController::performLogout(AccountStatus::message($accountStatus), Flash::DANGER);
    }
} else {
    define("USER_LOGGED", false);
    define("LOGGED_USER_PROFILE", false);
}

define("ADMIN_LOGGED", USER_LOGGED && LOGGED_USER_PROFILE !== false && LOGGED_USER_PROFILE['access_level'] === 'admin');
define("MODERATOR_LOGGED", USER_LOGGED && LOGGED_USER_PROFILE !== false && LOGGED_USER_PROFILE['access_level'] === 'moderator');

try {
    $router = new Router();
} catch (\Throwable $e) {
    throw new RuntimeException("Failed to initialize the router: " . $e->getMessage(), 0, $e);
}

try {
    require_once CONFIG['path'] . '/../routes.php';
} catch (\Throwable $e) {
    throw new RuntimeException("Failed to load routes file: " . $e->getMessage(), 0, $e);
}

try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch (\Throwable $e) {
    throw $e;
}

