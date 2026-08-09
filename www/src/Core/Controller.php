<?php
/**
 * Base class every controller extends.
 *
 * The router constructs it with the parameters from the matched route, which
 * stay available as $this->route_params. Provides the shared machinery: login
 * enforcement, redirects, reading POST values, and pagination.
 *
 * requireLogin() does not return a failure. It stores the requested page,
 * flashes a message and redirects to /login, so a controller calls it and then
 * carries on assuming a user. Redirects use 303 and exit immediately.
 *
 * getPostedString() and getPostedInteger() return false for missing or blank
 * input, so callers must compare with === false rather than truthiness.
 *
 * The pagination helpers read the page number from the route, not the query
 * string. Nothing calls them. The users page is paged in the browser instead.
 *
 * __call makes is_callable() true for any method name, so the router's check
 * for a missing action never fails and the error comes from here.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

use RuntimeException;
use App\Flash;
use App\Controllers\LoginController;

/**
 * Base controller for all application controllers.
 */
abstract class Controller
{
    /**
     * Parameters extracted from the matched route.
     *
     * @var array
     */
    protected array $route_params = [];

    /**
     * Controller constructor.
     *
     * @param array $route_params The parameters from the matched route
     * @return void
     */
    public function __construct(array $route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Handle calls to inaccessible methods.
     *
     * Only point a route at a public method. A protected one gets called
     * anyway, and a private one loops here until memory runs out.
     *
     * @param string $method The method name
     * @param array $args The method arguments
     * @return void
     * @throws RuntimeException If the method does not exist
     */
    public function __call(string $method, array $args): void
    {
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            $controllerClass = get_class($this);
            throw new RuntimeException("Method {$method} not found in controller {$controllerClass}");
        }
    }

    /**
     * Redirect to a given URL.
     *
     * @param string $url The URL to redirect to
     * @return never
     */
    public static function redirect(string $url): never
    {
        header('Location: ' . $url, true, 303);
        exit;
    }

    /**
     * Require the user to be logged in.
     *
     * @return void
     */
    public function requireLogin(): void
    {
        if (!defined('USER_LOGGED') || USER_LOGGED !== true) {
            Flash::addMessage('Please login', Flash::WARNING, 100000);
            LoginController::rememberRequestedPage();
            self::redirect(CONFIG['url'] . '/login');
        }
    }

    /**
     * Get a posted string value.
     *
     * @param string $key The POST key
     * @return string|false The trimmed string or false if not set/empty
     */
    public function getPostedString(string $key): string|false
    {
        return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? trim($_POST[$key]) : false;
    }

    /**
     * Get a posted integer value.
     *
     * @param string $key The POST key
     * @return int|false The integer value or false if not set/empty
     */
    public function getPostedInteger(string $key): int|false
    {
        return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? intval(trim($_POST[$key])) : false;
    }

    /**
     * Calculate the starting point for SQL LIMIT clauses.
     *
     * A page past the end of the results falls back to page one.
     *
     * @param array $pageLinkArguments Must contain 'results_per_page' and 'results_count'
     * @return int The zero-based starting index
     */
    public function resultsFrom(array $pageLinkArguments): int
    {
        $currentPage = isset($this->route_params['page']) ? intval($this->route_params['page']) : 1;
        $sqlLimitFrom = ($currentPage - 1) * $pageLinkArguments['results_per_page'];
        $maxLimit = $pageLinkArguments['results_count'] - 1;

        return ($sqlLimitFrom > $maxLimit) ? 0 : $sqlLimitFrom;
    }

    /**
     * Generate pagination links.
     *
     * The sides are not symmetric. The defaults give four links on the left and
     * three on the right.
     *
     * @param array $argumentsArray Must contain 'url', 'results_count', 'results_per_page'
     * @param int $linksLeft Links to show left of current page
     * @param int $linksRight One more than the links shown right of current page
     * @param bool $firstAndLastPageLinks Whether to show first/last page links
     * @return array Array of pagination link data
     */
    public function pageLinks(array $argumentsArray, int $linksLeft = 4, int $linksRight = 4, bool $firstAndLastPageLinks = false): array
    {
        $url = $argumentsArray['url'];
        $totalResults = $argumentsArray['results_count'];
        $resultsPerPage = $argumentsArray['results_per_page'];
        $currentPage = isset($this->route_params['page']) ? intval($this->route_params['page']) : 1;

        $totalPages = (int)ceil($totalResults / $resultsPerPage);

        if ($totalPages <= 1) {
            return [];
        }

        if ($currentPage > $totalPages) {
            $currentPage = 1;
        }

        $returnArray = [];

        // First page link
        if ($firstAndLastPageLinks && ($currentPage - $linksLeft) > 1) {
            $returnArray[] = ['href' => $url . '1', 'txt' => '&laquo;&laquo;', 'active' => '', 'disabled' => ''];
        }

        // Previous page link
        if ($currentPage == 1) {
            $returnArray[] = ['href' => '', 'txt' => '&laquo;', 'active' => '', 'disabled' => 'disabled'];
        } else {
            $returnArray[] = ['href' => $url . ($currentPage - 1), 'txt' => '&laquo;', 'active' => '', 'disabled' => ''];
        }

        // Left side page links
        for ($i = $currentPage - $linksLeft; $i <= $currentPage; $i++) {
            if ($i > 0) {
                $returnArray[] = [
                    'href' => ($i == $currentPage) ? '' : $url . $i,
                    'txt' => (string)$i,
                    'active' => ($i == $currentPage) ? 'active' : '',
                    'disabled' => ''
                ];
            }
        }

        // Right side page links
        for ($i = $currentPage + 1; $i < ($currentPage + $linksRight); $i++) {
            if ($i <= $totalPages) {
                $returnArray[] = [
                    'href' => $url . $i,
                    'txt' => (string)$i,
                    'active' => '',
                    'disabled' => ''
                ];
            }
        }

        // Next page link
        if ($currentPage < $totalPages) {
            $returnArray[] = ['href' => $url . ($currentPage + 1), 'txt' => '&raquo;', 'active' => '', 'disabled' => ''];
        } else {
            $returnArray[] = ['href' => '', 'txt' => '&raquo;', 'active' => '', 'disabled' => 'disabled'];
        }

        // Last page link
        if ($firstAndLastPageLinks && ($currentPage + $linksRight) < $totalPages) {
            $returnArray[] = ['href' => $url . $totalPages, 'txt' => '&raquo;&raquo;', 'active' => '', 'disabled' => ''];
        }

        return $returnArray;
    }

    /**
     * Get the user's IP address.
     *
     * The first three keys are headers, so a client can set them to anything.
     * Only REMOTE_ADDR comes from the server. This value feeds the login
     * throttle and is saved on new accounts.
     *
     * Behind two proxies X-Forwarded-For is a comma separated list, which fails
     * validation here and falls through to the next key.
     *
     * @return string The IP address or empty string if not found
     */
    public function userIp(): string
    {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }
        
        return '';
    }
}

