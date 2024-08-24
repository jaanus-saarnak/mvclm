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

namespace App\Controllers;

use Core\View;
use App\Flash;
use App\Models\LoginModel;
use App\Models\UsersModel;
use App\Models\AccessTokenModel;


/**
 * Login controller
 */
class LoginController extends \Core\Controller
{
    /**
     * @throws \Exception
     */
    public function index()
    {
        $postedUsername = $this->getPostedString('username');
        $postedPassword = $this->getPostedString('password');

        if ($postedUsername != false && $postedPassword != false) {

            /**
             * Limit brute force password guessing by limiting wrong passwords from one IP
             */
            $wrongPasswordsFromIp = LoginModel::countWrongPasswordsFromIp($this->userIp());

            if ($wrongPasswordsFromIp > CONFIG['wrong_passwords_limit_from_ip']) {

                Flash::addMessage(
                    'Wrong password has been entered more then ' . CONFIG['wrong_passwords_limit_from_ip'] . ' times from this IP in last 24 hours',
                    Flash::DANGER
                );

                $this->redirect(CONFIG['url']);
            }


            /**
             * Get user profile
             */
            $userProfile = UsersModel::getByUsername($postedUsername);


            /**
             * Verify password
             */
            if (isset($userProfile['password_hash']) && password_verify($postedPassword, $userProfile['password_hash'])) {

                $postedRemember = $this->getPostedString('remember');

                self::login($userProfile['uid'], $postedRemember);

                LoginModel::updateLastLoginTime($userProfile['uid']);

                LoginModel::saveLoginIp($this->userIp(), $userProfile['uid'], LoginModel::CORRECT);

                /**
                 * Detect users who login into multiple accounts from same browser
                 */
                self::detectLogInsFromThisBrowser($userProfile['uid'], $userProfile['logins_cookie_stats']);

                Flash::addMessage('You are logged in', Flash::SUCCESS);

                /**
                 * Go to page where login was asked, if there was one saved into session
                 */
                $this->redirect($this->getReturnToPage());

            } else {

                LoginModel::saveLoginIp(
                    $this->userIp(),
                    $userProfile['uid'] ?? '',
                    LoginModel::WRONG
                );

                Flash::addMessage('Enter correct Username and Password !', Flash::DANGER);

                $this->redirect(CONFIG['url'] . '/login');
            }

        } else {

            /**
             * Show login form
             */
            View::renderTemplate('/App/Views/login.php', []);
        }
    }


    /**
     * @param int $uid
     * @param string $remember
     * @throws \Exception
     */
    static function login($uid, $remember = '')
    {
        session_regenerate_id(true);

        $accessToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

        $_SESSION['user_id'] = $uid;
        $_SESSION['access_token'] = $accessToken;

        AccessTokenModel::add(
            $uid,
            $accessToken
        );

        if ($remember == 'on') {

            setcookie('uid', $uid, time() + 60 * 60 * 24 * 30, '/');

            setcookie('hash', $accessToken, time() + 60 * 60 * 24 * 30, '/');
        }
    }


    /**
     * @param int $uid
     * @param string $tokenFromCookie
     * @throws \Exception
     */
    static function loginFromCookie($uid, $tokenFromCookie)
    {
        $accessToken = AccessTokenModel::get($uid);

        if ($accessToken == $tokenFromCookie) {

            $_SESSION['user_id'] = $uid;

            $_SESSION['access_token'] = $accessToken;
        }
    }


    /**
     * @throws \Exception
     */
    public static function logout()
    {
        if (isset($_SESSION['user_id'])) {

            AccessTokenModel::delete($_SESSION['user_id']);
        }

        setcookie('uid', '', time() - 60*60*24*30, '/');
        setcookie('hash', '', time() - 60*60*24*30, '/');

        $_SESSION = [];

        unset($_SESSION['user_id']);
        unset($_SESSION['access_token']);

        //session_destroy();

        Flash::addMessage('You are logged out !', Flash::SUCCESS);

        self::redirect(CONFIG['url']);
    }


    /**
     * Get login status
     *
     * @return bool|int
     * @throws \Exception
     */
    static function userLogged()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['access_token'])) {

            return false;
        }

        if ($_SESSION['access_token'] == AccessTokenModel::get($_SESSION['user_id'])) {

            return $_SESSION['user_id'];

        } else {

            return false;
        }
    }


    /**
     * Save into session the url where login was asked
     *
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }


    /**
     * Get from session the URL where login was asked
     *
     * @return mixed
     */
    private function getReturnToPage()
    {
        if (isset($_SESSION['return_to'])) {

            $returnTo = $_SESSION['return_to'];

            unset($_SESSION['return_to']);

            return $returnTo;

        } else {

            return CONFIG['url'];
        }
    }


    /**
     * Detect users who have logged in into multiple user accounts from same browser
     *
     * @param int $userId
     * @param string $oldUidsFromDatabaseString
     * @throws \Exception
     */
    static function detectLogInsFromThisBrowser($userId, $oldUidsFromDatabaseString)
    {
        if (isset($_COOKIE['statistics'])) {

            /**
             * Get user ids from cookie
             */
            $uidsFromCookieString = $_COOKIE['statistics'];

            /**
             * Convert user ids string from statistics cookie into array
             */
            $uidsFromCookieArray = explode(",", $uidsFromCookieString);

            /**
             * Making sure user ids found from cookie are numbers
             */
            $uidsFromCookieArrayIntval = array_map('intval', $uidsFromCookieArray);


            /**
             * Convert user ids string from database into array
             */
            $uidsFromDatabaseArray = explode(",", $oldUidsFromDatabaseString);

            /**
             * Finding new user IDs from cookie, by comparing arrays
             */
            $newUidsArray = array_diff($uidsFromCookieArrayIntval, $uidsFromDatabaseArray);


            /**
             * Saving new user IDs into database
             */
            if (count($newUidsArray) > 0) {

                /**
                 * Convert new user ids found from cookie from array into string
                 */
                $newUidsString = implode(",", $newUidsArray);

                /**
                 * Adding new user ids to the ones saved before in database
                 */
                if ($oldUidsFromDatabaseString == '') {

                    $updatedUidsForDatabaseString = $newUidsString;
                } else {

                    $updatedUidsForDatabaseString = $oldUidsFromDatabaseString . "," . $newUidsString;
                }

                /**
                 * Adding updated list into users table
                 */
                LoginModel::updateLoginStats($userId, $updatedUidsForDatabaseString);
            }


            /**
             * If user id is not present in cookie, then add it into it
             */
            if (!in_array($userId, $uidsFromCookieArrayIntval)) {

                /**
                 * Add user id to cookie string
                 */
                $oldUidsFromCookieString = implode(",", $uidsFromCookieArrayIntval);

                if ($oldUidsFromCookieString == '') {

                    $updatedUidsForCookieString = $userId;
                } else {

                    $updatedUidsForCookieString = $oldUidsFromCookieString . "," . $userId;
                }

                /**
                 * Saving updated cookie
                 */
                setcookie('statistics', $updatedUidsForCookieString, time() + 60 * 60 * 24 * 30 * 12, '/');
            }

        } else {

            /**
             * Creating statistics cookie if one is not created before
             */
            setcookie('statistics', $userId, time() + 60 * 60 * 24 * 30 * 12, '/');
        }
    }
}

