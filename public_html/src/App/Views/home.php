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

use Core\View;

require_once(CONFIG['path'] . '/App/Views/header.php');
?>

<div class="body flex-grow-1 home-page">
    <div class="container">

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php
                /**
                 * Show Users
                 */
                foreach ($content['user_profiles'] as $row) {
                    ?>
                    <tr>
                        <td><a class="username-link" href="<?php echo CONFIG['url'] . "/" . $row['username']; ?>"><?php echo $row['username']; ?></a></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo date_format(date_create($row['registered']), 'Y-m-d H:i'); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <?php echo View::renderPageLinks($content['page_links']); ?>

    </div>
</div>

<?php
require_once(CONFIG['path'] . '/App/Views/footer.php');

