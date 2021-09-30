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
 * Access token model
 */
class AccessTokenModel
{
    /**
     * @param int $uid
     * @param string $token
     * @throws \Exception
     */
    static function add($uid, $token)
    {
        $uidIntVal = intval($uid);

        self::delete($uidIntVal);

        Model::sqlQueryPrepared("
            INSERT INTO `sessions` (`uid`, `accesstoken`, `created`) 
            VALUES ({$uidIntVal}, ?, NOW())
        ",
            [$token]
        );
    }


    /**
     * @param int $uid
     * @return string|bool
     * @throws \Exception
     */
    static function get($uid)
    {
        $uidIntVal = intval($uid);

        $result = Model::getSingleRow("
            SELECT * FROM `sessions` 
            WHERE `uid` = {$uidIntVal}
            AND `created` >= DATE(NOW()) - INTERVAL 7 DAY 
            ORDER BY `id` DESC 
            LIMIT 1
        ");

        if (isset($result['accesstoken']) && $result['accesstoken'] != '') {

            return $result['accesstoken'];

        } else {

            return false;
        }
    }


    /**
     * @param int $uid
     * @throws \Exception
     */
    static function delete($uid)
    {
        $uidIntVal = intval($uid);

        Model::sqlQuery("DELETE FROM `sessions` WHERE `uid` = {$uidIntVal}");
    }
}

