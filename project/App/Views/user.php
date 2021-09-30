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

<div id="userProfile">

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4>User Profile</h4><hr>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form>
                        <div class="form-group row">
                            <label for="username" class="col-4 col-form-label">Username</label>
                            <div class="col-8">
                                <input id="username" class="form-control" type="text" value="<?php echo $content['user_profile']['username']; ?>" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-4 col-form-label">Email</label>
                            <div class="col-8">
                                <input id="email" class="form-control" type="email" value="<?php echo $content['user_profile']['email']; ?>" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="time_registered" class="col-4 col-form-label">Registered</label>
                            <div class="col-8">
                                <input id="time_registered" class="form-control" type="text" value="<?php echo $content['user_profile']['registered']; ?>" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="time_last_login" class="col-4 col-form-label">Last login</label>
                            <div class="col-8">
                                <input id="time_last_login" class="form-control" type="text" value="<?php echo $content['user_profile']['last_login']; ?>" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="last_login_ip" class="col-4 col-form-label">Last login IP</label>
                            <div class="col-8">
                                <input id="last_login_ip" class="form-control" type="text" value="<?php echo $content['user_profile']['last_login_ip']; ?>" readonly />
                            </div>
                        </div>

                    </form>
                </div><!--/.col-md-12-->
            </div><!--/.row-->

        </div><!--/.card-body-->
    </div><!--/.card-->

<?php

if(ADMIN_LOGGED)
{
    ?>
    <p class="admin-area-label">Administrator options:</p>

    <div class="admin-area-description">
        Other users who have logged in from same browser as this user (info collected from cookies):<br>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                foreach ($content['users_with_common_browser'] as $row) {

                    if ($row->user_deleted) {

                        echo '<li class="breadcrumb-item"><a title="Deleted user" href="#">uid: ' . $row->uid . '</a></li>' . PHP_EOL;

                    } else {

                        echo '<li class="breadcrumb-item"><a href="' . CONFIG['url'] . '/' . $row->username . '">' . $row->username . '</a></li>' . PHP_EOL;
                    }

                }
                ?>
            </ol>
        </nav>
    </div>
    <?php
}


/**
 * Delete user account button
 */
if (ADMIN_LOGGED || LOGGED_USER_PROFILE['uid'] == $content['user_profile']['uid']) {
   ?>
    <a onclick="return confirm('DELETE USER ACCOUNT ?')" class="btn btn-outline-danger admin-delete-button" href="<?php echo CONFIG['url']; ?>/delete/<?php echo $content['user_profile']['uid']; ?>">Delete account</a>
    <?php
}

echo '</div>';

require_once(CONFIG['path'] . '/App/Views/footer.php');

