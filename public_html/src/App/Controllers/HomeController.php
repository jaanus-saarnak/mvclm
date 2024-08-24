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
 * Home controller
 */
class HomeController extends \Core\Controller
{
    /**
     * @throws \Exception
     * @return void
     */
    public function index()
    {
        /**
         * Generate page links array
         */
        $pageLinkArgs = [
            'url' => CONFIG['url'] . '/page-',
            'results_count' => UsersModel::count(),
            'results_per_page' => 5
        ];

        $pageLinks =  $this->pageLinks($pageLinkArgs);


        /**
         * Get user profiles
         */
        $userProfiles = UsersModel::get(
            $this->resultsFrom($pageLinkArgs),
            $pageLinkArgs['results_per_page']
        );


        /**
         * Render template
         */
        View::renderTemplate('/App/Views/home.php', [
            'user_profiles' => $userProfiles,
            'page_links' => $pageLinks
        ]);
    }
}

