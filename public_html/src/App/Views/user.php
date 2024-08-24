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

require_once(CONFIG['path'] . '/App/Views/header.php');
?>

<div class="body flex-grow-1">
    <div class="container-lg px-4">

        <div id="user-profile">
            <div class="container">

                <div class="row gutters-sm">
                    <div class="col-md-4 mb-3">

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column align-items-center text-center">
                                    <img src="<?php echo CONFIG['url']; ?>/assets/img/avatar.jpg" alt="Admin" class="rounded-circle" width="150">
                                    <div class="mt-3">
                                        <h4><?php echo $content['user_profile']['username']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-8">
                        <div class="card mb-3">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <?php echo $content['user_profile']['email']; ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Registered</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <?php echo $content['user_profile']['registered']; ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Last login</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <?php echo $content['user_profile']['last_login']; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php
                if(ADMIN_LOGGED)
                {
                    ?>
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
                }   ?>

            </div>
        </div>

    </div>
</div>

<?php
require_once(CONFIG['path'] . '/App/Views/footer.php');

