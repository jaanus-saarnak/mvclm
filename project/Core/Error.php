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


/**
 * Error and exception handler
 */
class Error
{
    /**
     * @param $level
     * @param $message
     * @param $file
     * @param $line
     * @throws \ErrorException
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        /**
         * To keep the @ operator working
         */
        if (error_reporting() !== 0) {

            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }


    /**
     * @param $exception
     * @throws \Exception
     */
    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode();

        if ($code != 404) {

            $code = 500; // General error
        }

        http_response_code($code);


        if (CONFIG['show_errors']) {

            /**
             * Show error details to visitor
             */
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
            
        } else {

            View::renderTemplate('/App/Views/' . $code . '.php', []);
        }


        /**
         * Save errors into log file
         */
        $log = CONFIG['path'] . '/logs/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $log);

        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

        error_log($message);        
    }
}

