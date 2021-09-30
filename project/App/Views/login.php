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
<div id="loginPage">

    <div class="card login-form">
        <form method="post" action="<?php echo CONFIG['url']; ?>/login">
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username"  required="required" value="admin" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required="required" value="password" />
            </div>

            <div id="checkbox">
                <input class="align-middle" type="checkbox" name="remember" id="remember-me" />
                <label for="remember-me">Remember</label>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

</div>
<?php

require_once(CONFIG['path'] . '/App/Views/footer.php');

