<?php
/**
 * MvcLM v1.0 - MVC Framework with Login Manager
 * Copyright (c) 2021 Jaanus Saarnak
 *
 * LICENSE AND DISCLAIMER
 *
 * THIS SOFTWARE CAN BE USED ON ONE DOMAIN FOR TESTING USE ONLY.
 * ALL OTHER RIGHTS ARE RESERVED.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Software website: https://www.MvcLM.com
 */

namespace Core;


/**
 * Router
 */
class Router
{
    /**
     * Associative array of routes (the routing table)
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Parameters from the matched route
     *
     * @var array
     */
    protected $params = [];


    /**
     * Add a route to the routing table
     *
     * @param string $route  The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public function add($route, $params = [])
    {
        /**
         * Convert the route to a regular expression: escape forward slashes
         */
        $route = preg_replace('/\//', '\\/', $route);

        /**
         * Convert variables e.g. {controller}
         */
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        /**
         * Convert variables with custom regular expressions, for integer: {uid:\d+} and for string: {username:\w+}
         */
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        /**
         * Add start and end delimiters, and case insensitive flag
         */
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }


    /**
     * Get all the routes from the routing table
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }


    /**
     * Match the route to the routes in the routing table, setting the $params property if a route is found.
     *
     * @param string $url The route URL
     * @return boolean true if a match found, false otherwise
     */
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {

            if (preg_match($route, $url, $matches)) {

                /**
                 * Get named capture group values
                 */
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
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }


    /**
     * Dispatch the route, creating the controller object and running the action method
     *
     * @param string $url The route URL
     * @throws \Exception
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {

            $controller = $this->params['controller'];

            $controller = $this->convertToStudlyCaps($controller);

            $controller = 'App\Controllers\\' . $controller;

            if (class_exists($controller)) {

                $controller_object = new $controller($this->params);

                $action = $this->params['action'];

                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) {

                    $controller_object->$action();

                } else {

                    throw new \Exception("Method $action (in controller $controller) not found");
                }

            } else {

                throw new \Exception("Controller class $controller not found");
            }

        } else {

            View::renderTemplate('/App/Views/404.php', []);
        }
    }


    /**
     * Convert the string with hyphens to StudlyCaps, post-authors => PostAuthors
     *
     * @param string $string The string to convert
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }


    /**
     * Convert the string with hyphens to camelCase, add-new => addNew
     *
     * @param string $string The string to convert
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }


    /**
     * Remove the query string variables from the URL
     *
     * @param string $url The full URL
     * @return string The URL with the query string variables removed
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {

            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {

                $url = $parts[0];

            } else {

                $url = '';
            }
        }

        return $url;
    }
}

