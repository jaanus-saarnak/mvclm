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

namespace App;


/**
 * Flash messages
 */
class Flash
{
    /**
     * Light grey message
     * @var string
     */
    const SECONDARY = 'secondary';

    /**
     * Yellow message
     * @var string
     */
    const WARNING = 'warning';

    /**
     * Red message
     * @var string
     */
    const DANGER = 'danger';

    /**
     * Green message
     * @var string
     */
    const SUCCESS = 'success';


    /**
     * @param string $message
     * @param string $type
     * @param int $timer
     */
    public static function addMessage($message, $type, $timer = 1500)
    {
        if (! isset($_SESSION['flash_notifications'])) {

            $_SESSION['flash_notifications'] = [];
        }

        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type,
            'timer' => $timer
        ];
    }


    /**
     * @return array|bool
     */
    public static function getMessages()
    {
        if (isset($_SESSION['flash_notifications'])) {

            $messages = $_SESSION['flash_notifications'];

            unset($_SESSION['flash_notifications']);

            return $messages;

        } else {

            return false;
        }
    }
}

