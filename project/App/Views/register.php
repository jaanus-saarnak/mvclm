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

                message.style.marginBottom = '-20px';

            } else {

                message.style.marginBottom = '0px';
            }
        });
    }

    function checkPassword() {

        let password = document.getElementById('password');
        let passwordAgain = document.getElementById('password-again');
        let username = document.getElementById('username');

        if (password.value !== passwordAgain.value) {
            alert("PASSWORDS DO NOT MATCH");
            return false;
        }

        if (password.value === username.value) {
            alert("DO NOT USE USERNAME AS YOUR PASSWORD");
            return false;
        }

        return true;
    }
</script>


<div id="registerPage">

    <div class="card register-form">
        <form action="<?php echo CONFIG['url']; ?>/register" method="post" onkeyup="checkThatUsernameIsNotTaken();">

            <div class="form-group">
                <input id="username" type="text" class="form-control" name="username" minlength="3" maxlength="16" placeholder="Username" <?php if (isset($_POST['username'])) { echo 'value="' . $_POST['username'] . '"'; } ?> required />
                <div id="username-status"></div>
            </div>

            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" <?php if (isset($_POST['email'])) { echo 'value="' . $_POST['email'] . '"'; } ?> required />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" minlength="6" maxlength="20" placeholder="Password" required />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password-again" name="password-again" minlength="6" maxlength="20" placeholder="Password again" required />
            </div>

            <button onclick="return checkPassword();" type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

</div>

<?php
require_once(CONFIG['path'] . '/App/Views/footer.php');

