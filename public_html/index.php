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

/**
 * Include app configs
 */
require_once 'config.php';

/**
 * Composer autoloader
 */ 
require_once CONFIG['path'] . '/vendor/autoload.php';

use App\Models\UsersModel;
use App\Controllers\LoginController;

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();


/**
 * Login from cookie
 */
if (isset($_COOKIE['uid']) && isset($_COOKIE['hash'])) {

    LoginController::loginFromCookie($_COOKIE['uid'], $_COOKIE['hash']);
}


/**
 * Save logged users profile into global variable
 */
if ($loggedUid = LoginController::userLogged()) {

    define("USER_LOGGED", true);
    define("LOGGED_USER_PROFILE", UsersModel::getByID($loggedUid));

} else {

    define("USER_LOGGED", false);
    define("LOGGED_USER_PROFILE", false);
}


/**
 * Use global variable for admin login status
 */
if (USER_LOGGED && LOGGED_USER_PROFILE['uid'] == CONFIG['admin_uid']) {

    define("ADMIN_LOGGED", true);

} else {

    define("ADMIN_LOGGED", false);
}


/**
 * Routing Table
 */
$router = new Core\Router();

/**
 * Home page
 */
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('page-{page:\d+}', ['controller' => 'HomeController', 'action' => 'index']);

/**
 * Login and logout
 */
$router->add('login', ['controller' => 'LoginController', 'action' => 'index']);
$router->add('logout', ['controller' => 'LoginController', 'action' => 'logout']);

/**
 * Search
 */
$router->add('search', ['controller' => 'SearchController', 'action' => 'index']);
$router->add('search/results', ['controller' => 'SearchController', 'action' => 'results']);

/**
 * Terms and Privacy
 */
$router->add('terms', ['controller' => 'TermsController', 'action' => 'index']);
$router->add('privacy', ['controller' => 'PrivacyController', 'action' => 'index']);

/**
 * Register users
 */
$router->add('register', ['controller' => 'RegisterController', 'action' => 'index']);
$router->add('ajax-username', ['controller' => 'AjaxController', 'action' => 'username']);

/**
 * Settings and delete users
 */
$router->add('settings', ['controller' => 'SettingsController', 'action' => 'index']);
$router->add('delete/{uid:\d+}', ['controller' => 'ProfileController', 'action' => 'delete']);

/**
 * User profile
 */
$router->add('{username:\w+}', ['controller' => 'ProfileController', 'action' => 'index']);
$router->dispatch($_SERVER['QUERY_STRING']);

