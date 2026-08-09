<?php
/**
 * Forgotten password controller
 *
 * Takes an address from an anonymous visitor, which is what makes this the one
 * mail path in the application that cannot identify who is asking. It answers
 * the same way whether or not the account exists, and it throttles per account
 * rather than per requester, because the requester cannot be trusted.
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
use App\PasswordReset;
use App\Models\UsersModel;
use App\Models\AccessTokenModel;
use App\Models\PasswordResetModel;

/**
 * Takes a request for a reset link and, if the address belongs to an account,
 * mails one.
 */
class PasswordResetController extends Controller
{
    /**
     * Show the form, or act on an address posted to it.
     *
     * The answer to a posted address is the same page every time. An account
     * that exists, an account that does not, one already holding a fresh link,
     * and one whose mail could not be sent all end here, because any difference
     * between them tells a stranger which addresses are registered.
     *
     * The honest limit, so nobody reads more into this than it does: the reply
     * is identical in content but not in timing, since a real account writes a
     * row and waits on SMTP. Closing that would need the send moved off the
     * request, and this framework has nowhere to move it to.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        if (!PasswordReset::isEnabled()) {
            $this->renderNotFound();
            return;
        }

        $postedEmail = $this->getPostedString('email');

        if ($postedEmail === false) {
            $this->renderForm();
            return;
        }

        if (CONFIG['demo_mode']) {
            Flash::addMessage('Resetting a password is switched off in this demo', Flash::WARNING);
            $this->renderForm();
            return;
        }

        $email = strtolower(trim($postedEmail));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::addMessage('Enter a valid email', Flash::DANGER);
            $this->renderForm();
            return;
        }

        $this->sendLinkIfAccountExists($email);

        try {
            View::renderTemplate('/App/Views/forgot-password-sent.php', ['email' => $email]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the reset confirmation: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Mail a link
     *
     * Every early return here is silent on purpose. The caller renders the same
     * page whatever happens, including when the send itself fails, which is
     * logged rather than shown.
     *
     * @param string $email The address as posted, lowercased and trimmed
     * @throws RuntimeException If the account lookup fails
     * @return void
     */
    private function sendLinkIfAccountExists(string $email): void
    {
        try {
            $userProfile = UsersModel::getByEmail($email);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to look up an account by email: " . $e->getMessage(), 0, $e);
        }

        if ($userProfile === null) {
            return;
        }

        $uid = intval($userProfile['uid']);

        if (PasswordResetModel::issuedRecently($uid)) {
            return;
        }

        try {
            PasswordReset::sendLink(
                $uid,
                (string) $userProfile['email'],
                (string) $userProfile['username']
            );
        } catch (\Throwable $e) {
            Error::logError($e);
        }
    }

    /**
     * Redeem a link and set a new password.
     *
     * The token is checked before anything is posted, so a dead link never
     * shows a form the visitor cannot submit. Unlike the verification link this
     * changes nothing on a GET: the link opens a form, and the form posts back
     * to the same address, which is where the token already is.
     *
     * @throws RuntimeException If the account cannot be read or written
     * @return void
     */
    public function reset(): void
    {
        if (!PasswordReset::isEnabled()) {
            $this->renderNotFound();
            return;
        }

        $token = strtolower((string) ($this->route_params['token'] ?? ''));

        $row = PasswordResetModel::findByToken($token);

        if ($row === null) {
            $this->renderFailure('unknown');
            return;
        }

        if (isset($row['used'])) {
            $this->renderFailure('used');
            return;
        }

        if (!PasswordResetModel::isUsable($row)) {
            $this->renderFailure('expired');
            return;
        }

        $postedPassword = $this->getPostedString('password');
        $postedPasswordAgain = $this->getPostedString('password-again');

        if ($postedPassword === false || $postedPasswordAgain === false) {
            $this->renderNewPasswordForm($token);
            return;
        }

        if (CONFIG['demo_mode']) {
            Flash::addMessage('Changing a password is switched off in this demo', Flash::WARNING);
            $this->renderNewPasswordForm($token);
            return;
        }

        $uid = intval($row['uid']);

        try {
            $userProfile = UsersModel::getByID($uid);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to read the account behind a reset token: " . $e->getMessage(), 0, $e);
        }

        if ($userProfile === null) {
            $this->renderFailure('unknown');
            return;
        }

        if (!$this->newPasswordIsValid($postedPassword, $postedPasswordAgain, (string) $userProfile['username'])) {
            $this->renderNewPasswordForm($token);
            return;
        }

        try {
            UsersModel::updatePassword($uid, $postedPassword);

            // Spend this link, then drop any other outstanding one.
            PasswordResetModel::markUsed(intval($row['id']));
            PasswordResetModel::revokeFor($uid);

            AccessTokenModel::delete($uid);

            UsersModel::markEmailVerified($uid);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to complete the password reset: " . $e->getMessage(), 0, $e);
        }

        Flash::addMessage('Your password has been changed. Please sign in.', Flash::SUCCESS);
        $this->redirect(CONFIG['url'] . '/login');
    }

    /**
     * Whether a posted password may replace the old one.
     *
     * The three rules are RegisterController's, deliberately identical rather
     * than improved, so an account cannot be given a password that signing up
     * would have refused.
     *
     * @param string $password The posted password
     * @param string $passwordAgain The posted confirmation
     * @param string $username The account's username
     * @return bool True if the password is acceptable
     */
    private function newPasswordIsValid(string $password, string $passwordAgain, string $username): bool
    {
        if (strlen($password) < 6 || strlen($password) > 20) {
            Flash::addMessage('Password should be 6-20 characters', Flash::DANGER);
            return false;
        }

        if ($username === $password) {
            Flash::addMessage('Do not use your username as a password', Flash::DANGER);
            return false;
        }

        if ($password !== $passwordAgain) {
            Flash::addMessage('Passwords do not match', Flash::DANGER);
            return false;
        }

        return true;
    }

    /**
     * Render the form that takes a new password.
     *
     * @param string $token The token from the URL, which the form posts back to
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function renderNewPasswordForm(string $token): void
    {
        try {
            View::renderTemplate('/App/Views/reset-password.php', ['token' => $token]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the new password form: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Explain why a link did not work.
     *
     * The three reasons stay distinct for the same reason the verification page
     * keeps them apart: they send a visitor to three different next steps.
     *
     * @param string $state One of 'unknown', 'used' or 'expired'
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function renderFailure(string $state): void
    {
        try {
            View::renderTemplate('/App/Views/reset-password-failed.php', ['state' => $state]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the reset link result: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Render the address form.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function renderForm(): void
    {
        try {
            View::renderTemplate('/App/Views/forgot-password.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the forgotten password form: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Answer as though the route did not exist.
     *
     * Used when the feature is switched off, so an install without SMTP does
     * not offer a page that could never work. Mirrors what Router does with a
     * path it cannot match.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function renderNotFound(): void
    {
        http_response_code(404);

        try {
            View::renderTemplate('/App/Views/404.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the not found page: " . $e->getMessage(), 0, $e);
        }
    }
}
