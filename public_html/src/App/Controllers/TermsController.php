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
use App\Models\UsersModel;


/**
 * Terms controller
 */
class TermsController extends \Core\Controller
{
    /**
     * @throws \Exception
     * @return void
     */
    public function index()
    {
        /**
         * Render template
         */
        View::renderTemplate('/App/Views/terms.php', []);
    }
}

