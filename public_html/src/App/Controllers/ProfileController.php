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
use App\Models\UsersModel;


/**
 * Profile controller
 */
class ProfileController extends \Core\Controller
{
    /**
     * @throws \Exception
     */
    public function index()
    {
        if (CONFIG['require_login_to_view_profile']) {

            $this->requireLogin();
        }

        /**
         * Get user profile
         */
        $userProfile = UsersModel::getByUsername($this->route_params['username']);

        if ($userProfile === false) {

            View::renderTemplate('/App/Views/404.php', []);

        } else {

            $usersWithCommonBrowser = [];

            if (ADMIN_LOGGED) {

                $usersWithCommonBrowser = self::getUsersWithCommonBrowser($userProfile['logins_cookie_stats'], $userProfile['uid']);
            }

            View::renderTemplate('/App/Views/user.php', [
                'user_profile' => $userProfile,
                'users_with_common_browser' => $usersWithCommonBrowser
            ]);
        }
    }


    /**
     * Delete user
     * @throws \Exception
     */
    public function delete()
    {
        if (
            !CONFIG['demo_mode'] &&
            ADMIN_LOGGED &&
            isset($this->route_params['uid'])
        ) {

            UsersModel::delete($this->route_params['uid']);
        }

        if (
            !CONFIG['demo_mode'] &&
            USER_LOGGED &&
            isset($this->route_params['uid']) &&
            $this->route_params['uid'] == LOGGED_USER_PROFILE['uid']
        ) {

            UsersModel::delete(LOGGED_USER_PROFILE['uid']);

            LoginController::logout();
        }

        if (CONFIG['demo_mode']) {

            Flash::addMessage('This function is not enabled in demo mode', Flash::DANGER, 100000);

        } else {

            Flash::addMessage('User deleted', Flash::SUCCESS, 3000);
        }

        $this->redirect(CONFIG['url']);
    }


    /**
     * @param string $uidsFromCookieStats
     * @param int $profileUid
     * @return array
     * @throws \Exception
     */
    static function getUsersWithCommonBrowser($uidsFromCookieStats, $profileUid)
    {
        $userIdsArray = explode(",", $uidsFromCookieStats);

        $return = [];
        foreach ($userIdsArray as $uid) {

            $uid = trim($uid);


            if ($uid != $profileUid && $uid != 0 && $uid != '') {

                $user = new \stdClass();

                $user->uid = $uid;

                if ($username = UsersModel::usernameFromUid($uid)) {

                    $user->username = $username;
                    $user->user_deleted = false;

                } else {

                    $user->username = $username;
                    $user->user_deleted = true;
                }

                $return[] = $user;
            }

        }

        return $return;
    }
}

