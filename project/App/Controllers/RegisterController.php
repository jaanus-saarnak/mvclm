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

namespace App\Controllers;

use Core\View;
use App\Flash;
use App\Models\UsersModel;


/**
 * Register controller
 */
class RegisterController extends \Core\Controller
{
    /**
     * @throws \Exception
     */
    public function index()
    {
        $postedUsername = $this->getPostedString('username');
        $postedEmail = $this->getPostedString('email');
        $postedPassword = $this->getPostedString('password');
        $postedPasswordAgain = $this->getPostedString('password-again');

        if ($postedUsername && $postedEmail && $postedPassword && $postedPasswordAgain) {

            $valuesValid = true;

            /**
             * Stop this function in demo mode
             */
            if (CONFIG['demo_mode']) {

                Flash::addMessage('This function is not enabled in demo mode', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Verify username characters
             */
            $postedUsername = strtolower($postedUsername);

            if (!preg_match("/^[A-Za-z0-9]{3,16}$/", $postedUsername)) {

                Flash::addMessage('Username should be 3-16 characters long and include only A-Z a-z 0-9', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Verify that username is not taken
             */
            if (UsersModel::getByUsername($postedUsername)) {

                Flash::addMessage('This username is taken', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Validate email
             */
            $postedEmail = strtolower($postedEmail);

            if (!filter_var($postedEmail, FILTER_VALIDATE_EMAIL)) {

                Flash::addMessage('Enter valid email', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Verify that email is not used before
             */
            if (UsersModel::getByEmail($postedEmail)) {

                Flash::addMessage('This email is already used', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Verify passwords length
             */
            if (strlen($postedPassword) < 6 || strlen($postedPassword) > 20) {

                Flash::addMessage('Password should be 6-20 characters', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Verify that username is not same as password
             */
            if ($postedUsername == $postedPassword) {

                Flash::addMessage('Do not use your username as password', Flash::DANGER, 100000);

                $valuesValid = false;
            }


            /**
             * Add new user
             */
            if ($valuesValid) {

                UsersModel::add($postedUsername, $postedPassword, $postedEmail, $this->userIp());

                Flash::addMessage('User account created', Flash::SUCCESS, 3000);

                $userId = UsersModel::getByUsername($postedUsername);

                LoginController::login($userId['uid']);

                $this->redirect(CONFIG['url'] . '/' . $postedUsername);
            }
        }


        /**
         * Show register form
         */
        View::renderTemplate('/App/Views/register.php', []);
    }
}

