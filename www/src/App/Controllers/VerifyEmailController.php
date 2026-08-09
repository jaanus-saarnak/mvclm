<?php
/**
 * Verify email page controller
 *
 * Redeems the link sent at sign-up. Reached by GET, because it is opened from a
 * mail client and nothing there can post. That is the one place in this
 * application where a GET changes state, and what makes it safe is the token:
 * unguessable, usable once, and dead after a day.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use Core\Error;
use Core\Controller;
use App\Flash;
use App\EmailVerification;
use App\Models\UsersModel;
use App\Models\EmailVerificationModel;

/**
 * Turns a verification link into a confirmed address.
 */
class VerifyEmailController extends Controller
{
    /**
     * Redeem the token in the URL and report what happened.
     *
     * The four outcomes are deliberately distinct. "Expired", "already used"
     * and "not a link we recognise" send a visitor to three different next
     * steps, and collapsing them into one failure page would hide which.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        // The route is compiled case insensitive, so an address bar that
        // shouted the token would match the pattern and then miss the hash.
        $token = strtolower((string) ($this->route_params['token'] ?? ''));

        $row = EmailVerificationModel::findByToken($token);

        if ($row === null) {
            $state = 'unknown';
        } elseif (isset($row['used'])) {
            $state = 'used';
        } elseif (!EmailVerificationModel::isUsable($row)) {
            $state = 'expired';
        } else {
            UsersModel::markEmailVerified((int) $row['uid']);
            EmailVerificationModel::markUsed((int) $row['id']);
            $state = 'verified';
        }

        try {
            View::renderTemplate('/App/Views/verify-email.php', ['state' => $state]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the verification result: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Send another link to an account that just proved its password.
     *
     * The recipient comes from the session marker LoginController sets, never
     * from the request. That is what keeps this from being an endpoint that
     * mails any address it is handed, and from answering whether an account
     * exists.
     *
     * @throws RuntimeException If the account cannot be read
     * @return void
     */
    public function resend(): void
    {
        // The router matches paths, not methods, and the CSRF gate only
        // inspects POSTs, so a GET would otherwise reach this untouched.
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->redirect(CONFIG['url'] . '/login');
        }

        $uid = isset($_SESSION[LoginController::PENDING_KEY])
            ? intval($_SESSION[LoginController::PENDING_KEY])
            : 0;

        if ($uid === 0) {
            Flash::addMessage('Sign in again and we will send you a new link', Flash::WARNING);
            $this->redirect(CONFIG['url'] . '/login');
        }

        try {
            $userProfile = UsersModel::getByID($uid);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to read the account asking for a new link: " . $e->getMessage(), 0, $e);
        }

        if ($userProfile === null) {
            unset($_SESSION[LoginController::PENDING_KEY]);
            Flash::addMessage('Sign in again and we will send you a new link', Flash::WARNING);
            $this->redirect(CONFIG['url'] . '/login');
        }

        if (isset($userProfile['email_verified_at'])) {
            unset($_SESSION[LoginController::PENDING_KEY]);
            Flash::addMessage('Your address is already confirmed. Please sign in.', Flash::SUCCESS);
            $this->redirect(CONFIG['url'] . '/login');
        }

        try {
            EmailVerification::sendLink(
                $uid,
                (string) $userProfile['email'],
                (string) $userProfile['username']
            );
            Flash::addMessage('A new confirmation link is on its way', Flash::SUCCESS);
        } catch (\Throwable $e) {
            Error::logError($e);
            Flash::addMessage('We could not send the link just now. Please try again shortly.', Flash::DANGER);
        }

        $this->redirect(CONFIG['url'] . '/login');
    }
}
