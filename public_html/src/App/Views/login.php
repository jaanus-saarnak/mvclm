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
    <div class="container">

        <main id="login">
            <div class="form">

                <form method="post" action="<?php echo CONFIG['url']; ?>/login">
                    <div class="login-header">Sign in</div>

                    <div class="card login-form floating-label">
                        <div class="input-field mb-4">
                            <input id="username" type="text" name="username" autoComplete="username" autofocus required />
                            <label for="username">Enter username</label>
                        </div>

                        <div class="input-field mb-4">
                            <input id="password" type="password" name="password" autoComplete="current-password" required />
                            <label for="password">Enter password</label>
                        </div>

                        <button type="submit" class="btn btn-primary login-button">Login</button>
                    </div>
                </form>

            </div>
        </main>

    </div>
</div>

<?php
require_once(CONFIG['path'] . '/App/Views/footer.php');

