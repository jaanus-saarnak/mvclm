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

namespace App\Models;

use Core\Model;


/**
 * Login model
 */
class LoginModel
{
    /**
     * Correct password
     * @var string
     */
    const CORRECT = 'correct';

    /**
     * Wrong password
     * @var string
     */
    const WRONG = 'wrong';


    /**
     * @param string $ip
     * @param int $uid
     * @param string $password
     * @throws \Exception
     */
    static function saveLoginIp($ip, $uid, $password)
    {
        $uidIntVal = intval($uid);

        Model::sqlQueryPrepared(
            "INSERT INTO `login_ip` (`uid`, `ip`, `password`) VALUES ({$uidIntVal}, ?, ?)",
            [$ip, $password]
        );
    }


    /**
     * @param string $ip
     * @return int|bool
     * @throws \Exception
     */
    static function countWrongPasswordsFromIp($ip)
    {
        Model::sqlQuery("DELETE FROM `login_ip` WHERE `time_added` >= DATE(NOW()) + INTERVAL 24 HOUR");

        $return = Model::getSingleRowPrepared(
            "SELECT count(`id`) AS passwords_count FROM `login_ip` WHERE `ip` = ? AND `password` = 'wrong'",
            [$ip]
        );

        return $return['passwords_count'];
    }


    /**
     * Detect users who login into multiple accounts from one browser
     *
     * @param int $uid
     * @param string $updatedUids
     * @throws \Exception
     */
    static function updateLoginStats($uid, $updatedUids)
    {
        $uidIntVal = intval($uid);

        Model::sqlQueryPrepared("UPDATE `bitdater_users_login` SET `logins_cookie_stats` = ? WHERE `uid` = {$uidIntVal}",
            [$updatedUids]
        );
    }


    /**
     * @param int $uid
     * @throws \Exception
     */
    static function updateLastLoginTime($uid)
    {
        $uidIntVal = intval($uid);

        Model::sqlQuery("UPDATE `users` SET `last_login` = NOW() WHERE `uid` = {$uidIntVal}");
    }
}

