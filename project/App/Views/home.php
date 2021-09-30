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

use Core\View;

require_once(CONFIG['path'] . '/App/Views/header.php');


echo '<div id="indexPage">';

echo View::renderPageLinks($content['page_links']);

/**
 * Show Users
 */
foreach ($content['user_profiles'] as $row) {

    echo '<div class="card"><div class="card-body">User: <a class="username-link" href="' . CONFIG['url'] . '/' . $row['username'] . '">' . $row['username'] . '</a></div></div>' . PHP_EOL;
}

echo '</div>';

require_once(CONFIG['path'] . '/App/Views/footer.php');

