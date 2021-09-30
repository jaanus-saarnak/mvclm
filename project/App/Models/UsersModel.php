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

namespace App\Models;

use Core\Model;


/**
 * Users model
 */
class UsersModel
{
    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $userIp
     * @throws \Exception
     */
    static function add($username, $password, $email, $userIp)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        Model::sqlQueryPrepared(
            "INSERT INTO users (`username`, `password_hash`, `email`, `last_login_ip`)
    		VALUES (?, ?, ?, ?)",
            [$username, $passwordHash, $email, $userIp]
        );
    }


    /**
     * @param int $usersFrom
     * @param int $showResults
     * @return array|bool
     * @throws \Exception
     */
    static function get($usersFrom, $showResults)
    {
        $usersFromIntVal = intval($usersFrom);
        $showResultsIntVal = intval($showResults);

        $userProfiles = Model::getMultiRow("SELECT * FROM `users` LIMIT {$usersFromIntVal}, {$showResultsIntVal}");

        return $userProfiles;
    }


    /**
     * @return int
     * @throws \Exception
     */
    static function count()
    {
        $result = Model::getSingleRow("SELECT count(`uid`) as users_count FROM `users`");

        return $result['users_count'];
    }


    /**
     * @param int $uid
     * @throws \Exception
     */
    static function delete($uid)
    {
        $uidIntVal = intval($uid);

        Model::sqlQuery("DELETE FROM `users` WHERE `uid` = {$uidIntVal}");
    }


    /**
     * @param string $username
     * @return array|bool|null
     * @throws \Exception
     */
    static function getByUsername($username)
    {
        $userProfile = Model::getSingleRowPrepared(
            "SELECT * FROM `users` WHERE `username` = ? LIMIT 1",
            [$username]
        );

        return $userProfile;
    }


    /**
     * @param string $email
     * @return array|bool|null
     * @throws \Exception
     */
    static function getByEmail($email)
    {
        $userProfile = Model::getSingleRowPrepared(
            "SELECT * FROM `users` WHERE `email` = ? LIMIT 1",
            [$email]
        );

        return $userProfile;
    }


    /**
     * @param int $uid
     * @return array|bool|null
     * @throws \Exception
     */
    static function getByID($uid)
    {
        $uidIntVal = intval($uid);

        $userProfile = Model::getSingleRow("SELECT * FROM `users` WHERE `uid` = {$uidIntVal} LIMIT 1");

        return $userProfile;
    }


    /**
     * @param int $uid
     * @return bool
     * @throws \Exception
     */
    static function usernameFromUid($uid)
    {
        $uidIntVal = intval($uid);

        if ($result = Model::getSingleRow("SELECT `username` FROM `users` WHERE `uid` = {$uidIntVal} LIMIT 1")) {

            return $result['username'];

        } else {

            return false;
        }
    }
}

