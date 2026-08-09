<?php
/**
 * Contact page controller
 *
 * One route serving both the form and its submission, the same shape
 * PasswordResetController uses. The page renders for everyone; only the sending
 * is refused, so a visitor never meets a route that has quietly disappeared.
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
use App\Contact;

/**
 * Shows the contact page and mails what is submitted to it.
 */
class ContactController extends Controller
{
    /**
     * The name of the field no person should ever fill in.
     *
     * Named after something a form plausibly asks for, because a field called
     * 'honeypot' is one a script knows to skip.
     */
    private const HONEYPOT_FIELD = 'website';

    /**
     * Display the contact page, or act on a message posted to it.
     *
     * The branch is on the request method rather than on whether a field
     * arrived. getPostedString() answers false for a field left blank, so
     * reading presence would make an empty form look like a fresh visit and
     * redraw the page with nothing said, which is a defect this application
     * already has on its register form.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    public function index(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->renderPage();
            return;
        }

        if (!Contact::isAvailable()) {
            $this->renderPage();
            return;
        }

        if (CONFIG['demo_mode']) {
            Flash::addMessage('Sending a message is switched off in this demo', Flash::WARNING);
            $this->renderPage();
            return;
        }

        $name = $this->singleLine((string) ($_POST['name'] ?? ''));
        $email = strtolower($this->singleLine((string) ($_POST['email'] ?? '')));
        $message = trim((string) ($_POST['message'] ?? ''));

        if ($this->singleLine((string) ($_POST[self::HONEYPOT_FIELD] ?? '')) !== '') {
            $this->finishAsSent();
            return;
        }

        if (!$this->submissionIsValid($name, $email, $message)) {
            $this->renderPage();
            return;
        }

        try {
            Contact::send($name, $email, $message);
        } catch (\Throwable $e) {
            Error::logError($e);
            Flash::addMessage('Your message could not be sent. Please try again later.', Flash::DANGER);
            $this->renderPage();
            return;
        }

        $this->finishAsSent();
    }

    /**
     * Whether a submission may be sent.
     *
     * One message per refusal and then a stop, so the page never lists
     * everything wrong with a form at once.
     *
     * @param string $name The posted name
     * @param string $email The posted address
     * @param string $message The posted message
     * @return bool True if all three fields are acceptable
     */
    private function submissionIsValid(string $name, string $email, string $message): bool
    {
        $nameLength = mb_strlen($name);

        if ($nameLength < Contact::NAME_MIN || $nameLength > Contact::NAME_MAX) {
            Flash::addMessage('Name should be ' . Contact::NAME_MIN . '-' . Contact::NAME_MAX . ' characters', Flash::DANGER);
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::addMessage('Enter a valid email', Flash::DANGER);
            return false;
        }

        $messageLength = mb_strlen($message);

        if ($messageLength < Contact::MESSAGE_MIN || $messageLength > Contact::MESSAGE_MAX) {
            Flash::addMessage('Message should be ' . Contact::MESSAGE_MIN . '-' . Contact::MESSAGE_MAX . ' characters', Flash::DANGER);
            return false;
        }

        return true;
    }

    /**
     * Say it was sent and leave, whether or not anything was.
     *
     * A redirect rather than a render, so a refresh repeats the visit instead of
     * the submission. The honeypot path ends here too, which is what makes a
     * discarded message indistinguishable from a delivered one.
     *
     * @return void
     */
    private function finishAsSent(): void
    {
        Flash::addMessage('Thank you. Your message has been sent.', Flash::SUCCESS);
        $this->redirect(CONFIG['url'] . '/contact');
    }

    /**
     * Flatten a posted value to one line.
     *
     * A name carrying a line break is what turns a composed subject into two
     * headers. Mailer strips them again before they reach a header, so this is
     * the second of two guards rather than the only one.
     *
     * @param string $value The value as posted
     * @return string The value with any run of whitespace reduced to one space
     */
    private function singleLine(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * Render the contact page.
     *
     * @throws RuntimeException If the page cannot be rendered
     * @return void
     */
    private function renderPage(): void
    {
        try {
            View::renderTemplate('/App/Views/contact.php', ['formIsAvailable' => Contact::isAvailable()]);
        } catch (\Throwable $e) {
            throw new RuntimeException("Failed to render the contact page: " . $e->getMessage(), 0, $e);
        }
    }
}
