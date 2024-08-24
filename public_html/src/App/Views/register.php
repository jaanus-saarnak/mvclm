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

<script>
    function checkThatUsernameIsNotTaken() {

        let request;
        let message = document.getElementById('username-status');

        request = $.ajax({
            url: "<?php echo CONFIG['url']; ?>/ajax-username",
            type: "post",
            data: {username: $('#username').val()}
        });

        request.done(function (response) {
            message.innerHTML = response;

            if (response) {
                message.style.marginBottom = '-16px';
            } else {
                message.style.marginBottom = '0px';
            }
        });
    }

    function checkPassword() {

        let password = document.getElementById('password');
        let passwordAgain = document.getElementById('password-again');

        if (password.value !== passwordAgain.value) {
            alert("PASSWORDS DO NOT MATCH");
            return false;
        }

        return true;
    }
</script>

<div class="body flex-grow-1">
    <div class="container-lg px-4">

        <main id="register">
            <div class="form">

                <form action="<?php echo CONFIG['url']; ?>/register" method="post" onkeyup="checkThatUsernameIsNotTaken();">
                    <div class="register-header">
                        Register account
                    </div>

                    <div class="card register-form floating-label">
                        <div class="input-field mb-4">
                            <input
                                id="username"
                                name="username"
                                type="text"
                                minlength="3"
                                maxlength="16"
                                autocomplete="off"
                                required
                                <?php if (isset($_POST['username'])) { echo 'value="' . $_POST['username'] . '"'; } ?>
                            />
                            <label for="username">Username</label>
                            <div id="username-status"></div>
                        </div>

                        <div class="input-field mb-4">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="off"
                                required
                                <?php if (isset($_POST['email'])) { echo 'value="' . $_POST['email'] . '"'; } ?>
                            />
                            <label for="email">Email</label>
                        </div>

                        <div class="input-field mb-4">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                minlength="6"
                                maxlength="20"
                                autocomplete="off"
                                required
                            />
                            <label for="password">Password</label>
                        </div>

                        <div class="input-field mb-4">
                            <input
                                id="password-again"
                                name="password-again"
                                type="password"
                                minlength="6"
                                maxlength="20"
                                autocomplete="off"
                            />
                            <label for="password-again">Password again</label>
                        </div>

                        <button type="submit" class="btn btn-primary login-button" onclick="return checkPassword();">Register</button>
                    </div>
                </form>

            </div>
        </main>

    </div>
</div>

<?php
require_once(CONFIG['path'] . '/App/Views/footer.php');

