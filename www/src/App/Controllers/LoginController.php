<?php
/**
 * Login controller
 *
 * Manages sign in, sign out, and who is signed in. User holds one access token
 * at a time, so signing in somewhere else ends the session.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use App\Flash;
use App\AccountStatus;
use App\EmailVerification;
use App\Models\LoginModel;
use App\Models\UsersModel;
use App\Models\AccessTokenModel;
use Core\Controller;
use Core\Csrf;

/**
 * Handles user authentication including login, logout, and session management.
 */
class LoginController extends Controller
{
    /**
     * Handle user login flow.
     *
     * @throws RuntimeException If any database or internal operation fails
     * @return void
     */
    public function index(): void
    {
        $postedUsername = $this->getPostedString('username');
        $postedPassword = $this->getPostedString('password');

        if ($postedUsername !== false && $postedPassword !== false) {
            try {
                $wrongPasswordsFromIp = intval(LoginModel::countWrongPasswordsFromIp($this->userIp()));
            } catch (\Throwable $e) {
                throw new RuntimeException("Failed to count wrong passwords: " . $e->getMessage(), 0, $e);
            }

            if ($wrongPasswordsFromIp > intval(CONFIG['wrong_passwords_limit_from_ip'])) {
                Flash::addMessage(
                    'Wrong password has been entered more than ' . intval(CONFIG['wrong_passwords_limit_from_ip']) . ' times from this IP in the last 24 hours',
                    Flash::DANGER
                );
                $this->redirect(CONFIG['url']);
            }

            try {
                $userProfile = UsersModel::getByUsername($postedUsername);
            } catch (\Throwable $e) {
                throw new RuntimeException("Failed to get user by username: " . $e->getMessage(), 0, $e);
            }

            if (isset($userProfile['password_hash']) && password_verify($postedPassword, $userProfile['password_hash'])) {
                $accountStatus = (string) ($userProfile['status'] ?? '');

                // Asked before the address check below, because an administrator
                // barring an account outranks it. Telling a suspended person to
                // go and confirm their email sends them somewhere pointless.
                if (AccountStatus::isBlocked($accountStatus)) {
                    // Logged as correct for the same reason the verification
                    // gate does: the password was right, so this attempt must
                    // not feed the wrong-password throttle for this address.
                    try {
                        LoginModel::saveLoginIp($this->userIp(), intval($userProfile['uid']), LoginModel::CORRECT);
                    } catch (\Throwable $e) {
                        throw new RuntimeException("Failed to log correct password: " . $e->getMessage(), 0, $e);
                    }

                    Flash::addMessage(AccountStatus::message($accountStatus), Flash::DANGER);
                    $this->redirect(CONFIG['url'] . '/login');
                }

                if (EmailVerification::isRequired() && !isset($userProfile['email_verified_at'])) {
                    // The password was right, so this is a correct sign-in
                    // attempt and must not count toward the wrong-password
                    // throttle for this address.
                    try {
                        LoginModel::saveLoginIp($this->userIp(), intval($userProfile['uid']), LoginModel::CORRECT);
                    } catch (\Throwable $e) {
                        throw new RuntimeException("Failed to log correct password: " . $e->getMessage(), 0, $e);
                    }

                    $this->askToConfirmAddress($userProfile);
                    return;
                }

                $postedRemember = $this->getPostedString('remember') === 'on';

                try {
                    self::login(intval($userProfile['uid']), $postedRemember);
                    LoginModel::updateLastLoginTime(intval($userProfile['uid']));
                    LoginModel::saveLoginIp($this->userIp(), intval($userProfile['uid']), LoginModel::CORRECT);
                    self::detectLogInsFromThisBrowser(intval($userProfile['uid']), $userProfile['logins_cookie_stats'] ?? '');
                } catch (\Throwable $e) {
                    throw new RuntimeException("Failed to complete login process: " . $e->getMessage(), 0, $e);
                }

                Flash::addMessage('You are logged in', Flash::SUCCESS);
                $this->redirect($this->getReturnToPage());
            } else {
                try {
                    LoginModel::saveLoginIp($this->userIp(), isset($userProfile['uid']) ? intval($userProfile['uid']) : 0, LoginModel::WRONG);
                } catch (\Throwable $e) {
                    throw new RuntimeException("Failed to log wrong password: " . $e->getMessage(), 0, $e);
                }

                Flash::addMessage('Enter correct Username and Password!', Flash::DANGER);
                $this->redirect(CONFIG['url'] . '/login');
            }
        } else {
            try {
                View::renderTemplate('/App/Views/login.php', []);
            } catch (\Throwable $e) {
                throw new RuntimeException("Failed to render login form: " . $e->getMessage(), 0, $e);
            }
        }
    }

    /**
     * The $_SESSION key naming an account that got its password right but has
     * not confirmed its address.
     *
     * This is what makes the resend safe. Only a visitor who has just proved
     * the password can ask for another link, so there is no endpoint that will
     * mail an arbitrary address on request, and nothing that reveals whether an
     * account exists.
     */
    public const PENDING_KEY = 'unverified_uid';

    /**
     * Tell an unconfirmed account that it cannot sign in yet.
     *
     * @param array $userProfile The row of the account that just authenticated
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function askToConfirmAddress(array $userProfile): void
    {
        $_SESSION[self::PENDING_KEY] = intval($userProfile['uid']);

        try {
            View::renderTemplate('/App/Views/verification-required.php', [
                'email' => (string) ($userProfile['email'] ?? ''),
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the confirmation notice: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Log the user in and set session and cookies if 'remember' is requested.
     *
     * @param int $uid User ID
     * @param bool $remember Whether to remember the user
     * @throws RuntimeException If the access token cannot be saved
     * @return void
     */
    public static function login(int $uid, bool $remember = false): void
    {
        session_regenerate_id(true);

        // Whoever is signing in has no unconfirmed business left.
        unset($_SESSION[self::PENDING_KEY]);

        // A token handed out before sign-in must not keep working after it.
        // Logout needs no equivalent: it empties $_SESSION and the next render
        // mints a fresh token.
        Csrf::rotate();

        $accessToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

        $_SESSION['user_id'] = $uid;
        $_SESSION['access_token'] = $accessToken;

        try {
            AccessTokenModel::add($uid, $accessToken);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to add access token: " . $e->getMessage(), 0, $e);
        }

        if ($remember) {
            setcookie('uid', (string)$uid, time() + 60 * 60 * 24 * 30, '/');
            setcookie('hash', $accessToken, time() + 60 * 60 * 24 * 30, '/');
        }
    }

    /**
     * Log the user in from a remembered cookie.
     *
     * @param int $uid User ID
     * @param string $tokenFromCookie The token from the user's cookie
     * @throws RuntimeException If token retrieval fails
     * @return void
     */
    public static function loginFromCookie(int $uid, string $tokenFromCookie): void
    {
        try {
            $accessToken = AccessTokenModel::get($uid);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to get access token from cookie login: " . $e->getMessage(), 0, $e);
        }

        if ($accessToken === $tokenFromCookie) {
            $_SESSION['user_id'] = $uid;
            $_SESSION['access_token'] = $accessToken;
        }
    }

    /**
     * Route action for /logout. Requires a POST.
     *
     * Call performLogout() from inside the application. This one is only for
     * the route, and refuses anything that is not a POST.
     *
     * @return void
     */
    public static function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            Flash::addMessage('Use the Sign out menu item to log out', Flash::WARNING);
            self::redirect(CONFIG['url']);
        }

        self::performLogout();
    }

    /**
     * End the current session: drop the access token, clear the cookies and the
     * session, then redirect home.
     *
     * This is the one to call from inside the application. ProfileController
     * uses it after an account deletes itself.
     *
     * A caller ending someone else's session for them, rather than at their
     * request, passes its own message. Telling a suspended account it is logged
     * out explains nothing it wanted to know.
     *
     * @param string|null $message Replaces the sign-out notice when given
     * @param string $type The flash type for that message (use Flash constants)
     * @throws RuntimeException If the access token deletion fails
     * @return void
     */
    public static function performLogout(?string $message = null, string $type = Flash::SUCCESS): void
    {
        if (isset($_SESSION['user_id'])) {
            try {
                AccessTokenModel::delete(intval($_SESSION['user_id']));
            } catch (\Throwable $e) {
                throw new RuntimeException("Failed to delete access token on logout: " . $e->getMessage(), 0, $e);
            }
        }

        setcookie('uid', '', time() - 60 * 60 * 24 * 30, '/');
        setcookie('hash', '', time() - 60 * 60 * 24 * 30, '/');

        $_SESSION = [];
        unset($_SESSION['user_id'], $_SESSION['access_token']);

        Flash::addMessage($message ?? 'You are logged out!', $type);

        self::redirect(CONFIG['url']);
    }

    /**
     * Check if user is logged in and their session is valid.
     *
     * @throws RuntimeException If retrieving the access token fails
     * @return bool|int Returns user ID if logged in, false otherwise
     */
    public static function userLogged(): bool|int
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['access_token'])) {
            return false;
        }

        try {
            $token = AccessTokenModel::get(intval($_SESSION['user_id']));
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to verify user login status: " . $e->getMessage(), 0, $e);
        }

        if ($_SESSION['access_token'] === $token) {
            return intval($_SESSION['user_id']);
        }

        return false;
    }

    /**
     * Remember the requested page so the user can return there after login.
     *
     * @return void
     */
    public static function rememberRequestedPage(): void
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'] ?? CONFIG['url'];
    }

    /**
     * Get the page the user was trying to access before being prompted to log in.
     *
     * @return string The URL to return to
     */
    private function getReturnToPage(): string
    {
        if (isset($_SESSION['return_to'])) {
            $returnTo = $_SESSION['return_to'];
            unset($_SESSION['return_to']);
            return $returnTo;
        }

        return CONFIG['url'] . '/dashboard';
    }

    /**
     * Detects and records if multiple users have been logged in from the same browser.
     *
     * @param int $userId Current user's UID
     * @param string $oldUidsFromDatabaseString Comma-separated string of previously recorded UIDs
     * @throws RuntimeException If updating login stats fails
     * @return void
     */
    public static function detectLogInsFromThisBrowser(int $userId, string $oldUidsFromDatabaseString): void
    {
        if (isset($_COOKIE['statistics'])) {
            $uidsFromCookieString = $_COOKIE['statistics'];
            $uidsFromCookieArray = explode(",", $uidsFromCookieString);
            $uidsFromCookieArrayIntval = array_map('intval', $uidsFromCookieArray);

            $uidsFromDatabaseArray = $oldUidsFromDatabaseString !== '' ? explode(",", $oldUidsFromDatabaseString) : [];
            $uidsFromDatabaseArray = array_map('intval', $uidsFromDatabaseArray);

            $newUidsArray = array_diff($uidsFromCookieArrayIntval, $uidsFromDatabaseArray);

            if (count($newUidsArray) > 0) {
                $newUidsString = implode(",", $newUidsArray);
                $updatedUidsForDatabaseString = ($oldUidsFromDatabaseString === '') ? $newUidsString : $oldUidsFromDatabaseString . "," . $newUidsString;

                try {
                    LoginModel::updateLoginStats($userId, $updatedUidsForDatabaseString);
                } catch (\Throwable $e) {
                    throw new RuntimeException("Failed to update login stats: " . $e->getMessage(), 0, $e);
                }
            }

            if (!in_array($userId, $uidsFromCookieArrayIntval, true)) {
                $oldUidsFromCookieString = implode(",", $uidsFromCookieArrayIntval);
                $updatedUidsForCookieString = ($oldUidsFromCookieString === '') ? (string)$userId : $oldUidsFromCookieString . "," . $userId;

                setcookie('statistics', $updatedUidsForCookieString, time() + 60 * 60 * 24 * 30 * 12, '/');
            }
        } else {
            setcookie('statistics', (string)$userId, time() + 60 * 60 * 24 * 30 * 12, '/');
        }
    }
}

