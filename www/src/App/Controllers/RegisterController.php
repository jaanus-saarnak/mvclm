<?php
/**
 * Register page controller
 *
 * Renders the sign-up form and handles its submission. Validation does not stop
 * at the first problem, so a bad submission reports everything wrong with it at
 * once. Username and email are lowercased in place, which is why both are taken
 * by reference. A successful registration signs the new account in and sends it
 * to its own profile.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Core\View;
use Core\Error;
use App\Flash;
use App\EmailVerification;
use App\Models\UsersModel;
use Core\Controller;

/**
 * Handles user registration and account creation.
 */
class RegisterController extends Controller
{
    /**
     * Display the registration page or handle form submission.
     *
     * @throws RuntimeException If database operations or template rendering fails
     * @return void
     */
    public function index(): void
    {
        $postedUsername = $this->getPostedString('username');
        $postedEmail = $this->getPostedString('email');
        $postedPassword = $this->getPostedString('password');
        $postedPasswordAgain = $this->getPostedString('password-again');
        $termsAccepted = $this->getPostedString('terms') !== false;

        if ($postedUsername !== false && $postedEmail !== false && $postedPassword !== false && $postedPasswordAgain !== false) {
            if ($this->validateRegistration($postedUsername, $postedEmail, $postedPassword, $postedPasswordAgain, $termsAccepted)) {
                $this->createAccount($postedUsername, $postedEmail, $postedPassword);
                return;
            }
        }

        try {
            View::renderTemplate('/App/Views/register.php', []);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render registration form: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Validate registration form data.
     *
     * @param string $username The posted username
     * @param string $email The posted email
     * @param string $password The posted password
     * @param string $passwordAgain The password confirmation
     * @param bool $termsAccepted Whether the terms checkbox was ticked
     * @throws RuntimeException If the username or email lookup fails
     * @return bool True if all validations pass
     */
    private function validateRegistration(string &$username, string &$email, string $password, string $passwordAgain, bool $termsAccepted): bool
    {
        $isValid = true;

        if (CONFIG['demo_mode']) {
            Flash::addMessage('This function is not enabled in demo mode', Flash::DANGER, 100000);
            return false;
        }

        $username = strtolower($username);
        if (!preg_match("/^[A-Za-z0-9]{3,16}$/", $username)) {
            Flash::addMessage('Username should be 3-16 characters long and include only A-Z a-z 0-9', Flash::DANGER, 100000);
            $isValid = false;
        }

        try {
            $existingUserByUsername = UsersModel::getByUsername($username);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to check if username is taken: " . $e->getMessage(), 0, $e);
        }

        if ($existingUserByUsername !== null) {
            Flash::addMessage('This username is taken', Flash::DANGER, 100000);
            $isValid = false;
        }

        $email = strtolower($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::addMessage('Enter a valid email', Flash::DANGER, 100000);
            $isValid = false;
        }

        try {
            $existingUserByEmail = UsersModel::getByEmail($email);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to check if email is used: " . $e->getMessage(), 0, $e);
        }

        if ($existingUserByEmail !== null) {
            Flash::addMessage('This email is already used', Flash::DANGER, 4000);
            $isValid = false;
        }

        if (strlen($password) < 6 || strlen($password) > 20) {
            Flash::addMessage('Password should be 6-20 characters', Flash::DANGER, 100000);
            $isValid = false;
        }

        if ($username === $password) {
            Flash::addMessage('Do not use your username as a password', Flash::DANGER, 100000);
            $isValid = false;
        }

        if ($password !== $passwordAgain) {
            Flash::addMessage('Passwords do not match', Flash::DANGER, 100000);
            $isValid = false;
        }

        if (!$termsAccepted) {
            Flash::addMessage('Please accept the Terms of Service and Privacy Policy to continue', Flash::DANGER, 100000);
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Create a new account, then either sign it in or ask it to confirm.
     *
     * @param string $username The validated username
     * @param string $email The validated email
     * @param string $password The user's password
     * @throws RuntimeException If user creation or login fails
     * @return void
     */
    private function createAccount(string $username, string $email, string $password): void
    {
        try {
            UsersModel::add($username, $password, $email, $this->userIp());
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to create new user: " . $e->getMessage(), 0, $e);
        }

        try {
            $userProfile = UsersModel::getByUsername($username);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to retrieve new user ID: " . $e->getMessage(), 0, $e);
        }

        if (!isset($userProfile['uid'])) {
            throw new RuntimeException("New user ID not found after creation.");
        }

        if (EmailVerification::isRequired()) {
            $this->askForConfirmation(intval($userProfile['uid']), $username, $email);
            return;
        }

        Flash::addMessage('User account created', Flash::SUCCESS, 3000);

        try {
            LoginController::login(intval($userProfile['uid']));
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to log in new user: " . $e->getMessage(), 0, $e);
        }

        $this->redirect(CONFIG['url'] . '/' . $username);
    }

    /**
     * Mail a verification link and render the page that says so.
     *
     * A send that fails does not undo the account. Deleting a just-created user
     * because a socket timed out is worse than leaving one that cannot sign in
     * yet, and signing in is the resend path, so the visitor is not stranded.
     *
     * @param int $uid The new account's identifier
     * @param string $username The account's username
     * @param string $email Where the link was sent
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function askForConfirmation(int $uid, string $username, string $email): void
    {
        $sent = true;

        try {
            EmailVerification::sendLink($uid, $email, $username);
        } catch (\Throwable $e) {
            $sent = false;
            Error::logError($e);
        }

        try {
            View::renderTemplate('/App/Views/verification-sent.php', [
                'email' => $email,
                'sent' => $sent,
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the verification notice: " . $e->getMessage(), 0, $e);
        }
    }
}

