<?php
/**
 * Flash messages, stored in the session
 *
 * View::renderTemplate() collects these on every render and reading clears
 * them, so a message queued before a redirect shows once and never again. The
 * layout builds its css class as alert-<type>, so a new type constant also
 * needs a matching rule in assets/css/app-source.css.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);
namespace App;

use RuntimeException;

/**
 * Handles short-lived messages that survive one redirect.
 */
class Flash
{
    public const SECONDARY = 'secondary';
    public const WARNING = 'warning';
    public const DANGER = 'danger';
    public const SUCCESS = 'success';

    /**
     * Add a flash message to the session.
     *
     * @param string $message The message text.
     * @param string $type The message type (use the class constants).
     * @param int $timer The duration (in milliseconds) that the message should be displayed.
     * @return void
     */
    public static function addMessage(string $message, string $type, int $timer = 2250): void
    {
        if (!isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type,
            'timer' => $timer
        ];
    }

    /**
     * Retrieve all flash messages from the session.
     *
     * After retrieving the flash messages, this method clears them from the session
     * so they won't persist beyond one page load.
     *
     * @return array|bool Returns an array of flash messages if present, false otherwise.
     */
    public static function getMessages(): array|bool
    {
        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);
            return $messages;
        }

        return false;
    }
}

