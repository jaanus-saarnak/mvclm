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
use App\Models\SearchModel;


/**
 * Search controller
 */
class SearchController extends \Core\Controller
{
    /**
     * Show search form
     * @throws \Exception
     */
    public function index()
    {
        View::renderTemplate('/App/Views/search.php', []);
    }


    /**
     * Show search results
     * @throws \Exception
     */
    public function results()
    {
        $searchResults = SearchModel::search(
            $this->getPostedString('username'),
            $this->getPostedString('email'),
            $this->getPostedString('ip')
        );

        View::renderTemplate('/App/Views/search.php', [
            'search_results'  => $searchResults
        ]);
    }
}

