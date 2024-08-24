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
 * Search model
 */
class SearchModel
{
    /**
     * @param string|bool $username
     * @param string|bool $email
     * @param string|bool $ip
     * @return array|bool
     * @throws \Exception
     */
    static function search($username = false, $email = false, $ip = false)
    {
        $bindVariablesArray = [];

        $usernameSql = $emailSql = $ipSql = '';

        if ($username) {

            $bindVariablesArray[] = "%{$username}%";

            $usernameSql = " AND username LIKE ? ";
        }

        if ($email) {

            $bindVariablesArray[] = "%{$email}%";

            $emailSql = " AND email LIKE ? ";
        }

        if ($ip) {

            $bindVariablesArray[] = "%{$ip}%";

            $ipSql = " AND last_login_ip LIKE ? ";
        }

        $return = Model::getMultiRowPrepared(
            "SELECT * FROM `users` WHERE `uid` > 0 " . $usernameSql . $emailSql . $ipSql,
            $bindVariablesArray
        );

        return $return;
    }
}

