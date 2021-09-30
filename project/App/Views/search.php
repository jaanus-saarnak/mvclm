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

require_once(CONFIG['path'] . '/App/Views/header.php');

?>
<div id="searchPage">

    <div class="card search-form">
        <form action="<?php echo CONFIG['url']; ?>/search/results" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username" <?php if (isset($_POST['username'])) { echo 'value="' . $_POST['username'] . '"'; } ?> />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="email" placeholder="Email" <?php if (isset($_POST['email'])) { echo 'value="' . $_POST['email'] . '"'; } ?> />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="ip" placeholder="IP" <?php if (isset($_POST['ip'])) { echo 'value="' . $_POST['ip'] . '"'; } ?> />
            </div>

            <button type="submit" name="search" class="btn btn-primary">Search</button>
        </form>
    </div>

    <?php
    if (isset($content['search_results']) && count($content['search_results']) > 0) {

        foreach ($content['search_results'] as $row) {
            ?>
            <div class="card">
                <div class="card-body">
                    User: <a class="username-link" href="<?php echo CONFIG['url'] . "/" . $row['username']; ?>"><?php echo $row['username']; ?></a>
                    <span class="user-id">(id:<?php echo $row['uid']; ?>)</span>
                </div>
            </div>
            <?php
        }
    }

echo '</div>';

require_once(CONFIG['path'] . '/App/Views/footer.php');

