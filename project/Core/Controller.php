<?php
/**
 * MvcLM v1.0 - MVC Framework with Login Manager
 *
 * Release date: 2021.09.30
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

use App\Flash;
use App\Controllers\LoginController;


/**
 * Base controller
 */
abstract class Controller
{
    /**
     * Parameters from matched route
     * @var array
     */
    protected $route_params = [];


    /**
     * @param array $route_params Parameters from the route
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }


    /**
     * Throw Exception if non-existent method is called
     *
     * @param $method
     * @param $args
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {

            call_user_func_array([$this, $method], $args);

        } else {

            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }


    /**
     * @param string $url
     */
    public static function redirect($url)
    {
        header('Location: ' . $url, true, 303);
        exit;
    }


    /**
     * @throws \Exception
     */
    public function requireLogin()
    {
        if (!USER_LOGGED) {

            Flash::addMessage('Please login', Flash::WARNING, 100000);

            LoginController::rememberRequestedPage();

            $this->redirect(CONFIG['url'] . '/login');
        }
    }


    /**
     * Get posted string
     *
     * @param $key
     * @return string|bool
     */
    function getPostedString($key) {

        if (isset($_POST[$key]) && trim($_POST[$key]) != '') {

            return trim($_POST[$key]);

        } else {

            return false;
        }
    }


    /**
     * Get posted integer
     *
     * @param $key
     * @return int|bool
     */
    function getPostedInteger($key) {

        if (isset($_POST[$key]) && trim($_POST[$key]) != '') {

            return intval(trim($_POST[$key]));

        } else {

            return false;
        }
    }


    /**
     * Get results from limit for SQL query, when using page link
     *
     * @param array $pageLinkArguments
     * @return int
     */
    function resultsFrom($pageLinkArguments) {

        $currentPage = (isset($this->route_params['page'])) ? intval(trim($this->route_params['page'])) : 1;

        $currentPageMinusOne = $currentPage - 1;

        $sqlLimitFrom = $currentPageMinusOne * $pageLinkArguments['results_per_page'];

        $maxLimit = $pageLinkArguments['results_count'] - 1;

        if ($sqlLimitFrom > $maxLimit) {
            $sqlLimitFrom = 0;
        }

        return $sqlLimitFrom;
    }


    /**
     * Generate page links array
     *
     * @param array $argumentsArray
     * @param int $linksLeft
     * @param int $linksRight
     * @param bool $firstAndLastPageLinks
     * @return array
     */
    function pageLinks($argumentsArray,  $linksLeft = 4, $linksRight = 4, $firstAndLastPageLinks = false)
    {
        $url = $argumentsArray['url'];
        $totalResults = $argumentsArray['results_count'];
        $resultsPerPage = $argumentsArray['results_per_page'];
        $currentPage = (isset($this->route_params['page'])) ? intval(trim($this->route_params['page'])) : 1;


        $totalPages = ceil($totalResults / $resultsPerPage);

        if ($totalPages <= 1) {

            return [];
        }

        /**
         * If given page number is too big, set it to one
         */
        if ($currentPage > $totalPages) {

            $currentPage = 1;
        }


        $returnArray = [];

        /**
         * Link to first page
         */
        if ($firstAndLastPageLinks && ($currentPage - $linksLeft) > 1) {

            array_push($returnArray, ['href' => $url . 1, 'txt' => '&laquo;&laquo;', 'active' => '', 'disabled' => '']);
        }

        /**
         * Link to previous page if current page is first page (show disabled link)
         */
        if ($currentPage == 1) {

            array_push($returnArray, ['href' => '', 'txt' => '&laquo;', 'active' => '', 'disabled' => 'disabled']);
        }

        /**
         * Link to previous page
         */
        if ($currentPage > 1) {

            array_push($returnArray, ['href' => $url . ($currentPage - 1), 'txt' => '&laquo;', 'active' => '', 'disabled' => '']);
        }

        /**
         * Left side links
         */
        for ($i = $currentPage - $linksLeft; $i <= $currentPage; $i++) {

            if ($i > 0) {

                if ($i == $currentPage) {

                    array_push($returnArray, ['href' => '', 'txt' => $i, 'active' => 'active', 'disabled' => '']);

                } else {

                    array_push($returnArray, ['href' => $url . $i, 'txt' => $i, 'active' => '', 'disabled' => '']);
                }
            }
        }

        /**
         * Right side links
         */
        for ($i = $currentPage + 1; $i < ($currentPage + $linksRight); $i++) {

            if ($i <= $totalPages) {

                if ($i == $currentPage) {

                    array_push($returnArray, ['href' => '', 'txt' => $i, 'active' => 'active', 'disabled' => '']);

                } else {

                    array_push($returnArray, ['href' => $url . $i, 'txt' => $i, 'active' => '', 'disabled' => '']);
                }
            }
        }

        /**
         * Link to next page
         */
        if ($currentPage != $totalPages && $currentPage < $totalPages) {

            array_push($returnArray, ['href' => $url . ($currentPage + 1), 'txt' => '&raquo;', 'active' => '', 'disabled' => '']);
        }

        /**
         * Link to next page if current page is last page (show disabled link)
         */
        if ($currentPage >= $totalPages) {

            array_push($returnArray, ['href' => '', 'txt' => '&raquo;', 'active' => '', 'disabled' => 'disabled']);
        }

        /**
         * Link to last page
         */
        if ($firstAndLastPageLinks && ($currentPage + $linksRight) < $totalPages) {

            array_push($returnArray, ['href' => $url . $totalPages, 'txt' => '&raquo;&raquo;', 'active' => '', 'disabled' => '']);
        }

        return $returnArray;
    }


    /**
     * @return string
     */
    function userIp()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {

            return $_SERVER['HTTP_CF_CONNECTING_IP'];

        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {

            return $_SERVER['HTTP_CLIENT_IP'];

        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {

            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        } elseif(isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {

            return $_SERVER['REMOTE_ADDR'];

        } else {

            return '';
        }
    }
}

