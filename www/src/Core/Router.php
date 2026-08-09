<?php
/**
 * URL router
 *
 * .htaccess rewrites any request that is not a real file, directory or link
 * into index.php?<path>, so the path arrives here as the query string. Patterns
 * are registered in routes.php and take {name} or {name:regex} parameters. A
 * match resolves to a class under App\Controllers, hyphenated segments becoming
 * StudlyCaps and the action camelCase. Every pattern is compiled case
 * insensitive, so /ABOUT reaches AboutController exactly as /about does.
 *
 * A URL matching nothing sets a 404 status, renders the 404 view here and
 * returns, so a missing page is not an application error. A route naming a
 * controller that does not exist throws instead. A route naming a missing
 * action also throws, but the error comes from Controller, not from here.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

use RuntimeException;
use Core\View;

/**
 * Maps URLs to controller actions based on defined routes.
 */
class Router
{
    /**
     * The routing table.
     *
     * @var array<string,array<mixed>>
     */
    protected array $routes = [];

    /**
     * Parameters from the matched route.
     *
     * @var array<string,mixed>
     */
    protected array $params = [];

    /**
     * Add a route to the routing table.
     *
     * The pattern is compiled to a regex here, once. A bare {name} matches
     * letters and hyphens. {name:regex} takes the pattern it is given, and every
     * route in routes.php uses that form.
     *
     * @param string $route Route pattern with optional parameters
     * @param array<mixed> $params Route parameters
     * @return void
     */
    public function add(string $route, array $params = []): void
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^}]+)\}/', '(?P<$1>$2)', $route);
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Get all defined routes.
     *
     * @return array<string,array<mixed>> The routing table
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Match URL against defined routes.
     *
     * A match also stores the parameters for getParams(), which is empty until
     * then.
     *
     * @param string $url The URL to match
     * @return bool True if matched
     */
    public function match(string $url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Get matched route parameters.
     *
     * @return array<string,mixed> Route parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Dispatch URL to controller action.
     *
     * @param string $url The URL to dispatch
     * @return void
     * @throws RuntimeException If the route names no controller or action,
     * or names one that does not exist
     */
    public function dispatch(string $url): void
    {
        $url = $this->removeQueryStringVariables($url);

        if (!$this->match($url)) {
            http_response_code(404);
            View::renderTemplate('/App/Views/404.php', []);
            return;
        }

        if (!isset($this->params['controller'])) {
            throw new RuntimeException('No controller specified for this route.');
        }
        
        if (!isset($this->params['action'])) {
            throw new RuntimeException('No action specified for this route.');
        }

        $controller = $this->convertToStudlyCaps((string)$this->params['controller']);
        $controller = 'App\\Controllers\\' . $controller;

        if (!class_exists($controller)) {
            throw new RuntimeException("Controller class {$controller} not found");
        }

        $controller_object = new $controller($this->params);
        $action = $this->convertToCamelCase((string)$this->params['action']);

        if (!is_callable([$controller_object, $action])) {
            throw new RuntimeException("Method {$action} (in controller {$controller}) not found");
        }

        $controller_object->$action();
    }

    /**
     * Convert hyphenated string to StudlyCaps.
     *
     * @param string $string The string to convert
     * @return string StudlyCaps string
     */
    protected function convertToStudlyCaps(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert hyphenated string to camelCase.
     *
     * @param string $string The string to convert
     * @return string camelCase string
     */
    protected function convertToCamelCase(string $string): string
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /**
     * Remove query string variables from the path.
     *
     * The path arrives as the query string, joined to any real variables by an
     * ampersand. An equals sign in the first part means there was no path, and
     * the empty string returned then is what the home route matches.
     *
     * @param string $url The path as received, with any query string appended
     * @return string The path on its own
     */
    protected function removeQueryStringVariables(string $url): string
    {
        if ($url === '') {
            return $url;
        }

        $parts = explode('&', $url, 2);
        
        return (strpos($parts[0], '=') === false) ? $parts[0] : '';
    }
}

