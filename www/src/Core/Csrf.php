<?php
/**
 * Cross-site request forgery tokens.
 *
 * One token per session, minted on first use and kept in $_SESSION beside the
 * flash messages. Templates reach it through two helpers. field() renders the
 * hidden input a form needs. token() gives both layouts the value for their
 * meta element, which is where JavaScript reads it.
 *
 * Verification happens once, in index.php, before the router runs. Every POST is
 * checked there rather than inside the controllers, so a form added later is
 * covered whether or not its author knows this class exists.
 *
 * rotate() is called when the session identity changes, which today means at
 * login. Logout empties $_SESSION outright, so the next render mints a fresh
 * token with no help.
 *
 * random_bytes() rather than the openssl call used for access tokens. It is the
 * documented cryptographically secure source and throws rather than quietly
 * returning weak output.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

/**
 * Mints, stores and verifies the per-session CSRF token.
 */
class Csrf
{
    /**
     * The $_SESSION key holding the token.
     */
    private const SESSION_KEY = 'csrf_token';

    /**
     * The form field name that carries the token back to the server.
     */
    public const FIELD_NAME = 'csrf_token';

    /**
     * Get the session's token, minting one if it does not have it yet.
     *
     * @return string The token as a hex string
     */
    public static function token(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY]) || $_SESSION[self::SESSION_KEY] === '') {
            return self::rotate();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Replace the session's token with a new one.
     *
     * Called where the session identity changes, so a token handed out before
     * a login cannot be used after it.
     *
     * @return string The new token
     */
    public static function rotate(): string
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Check a submitted value against the session's token.
     *
     * Deliberately does not mint. A session with no token yet fails, which is
     * the right answer for a request that arrived without one.
     *
     * @param mixed $submitted The value posted back, of whatever type it arrived as
     * @return bool True only if the session holds a token and the value matches it
     */
    public static function verify(mixed $submitted): bool
    {
        if (!is_string($submitted) || $submitted === '') {
            return false;
        }

        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY]) || $_SESSION[self::SESSION_KEY] === '') {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $submitted);
    }

    /**
     * Render the hidden input a posting form needs.
     *
     * Views call this rather than writing the markup, so the field name and the
     * escaping live in one place and a form cannot carry a subtly wrong one.
     *
     * @return string The hidden input element
     */
    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }
}
