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
?>

<div class="modal fade" id="loginModal" role="dialog" aria-hidden="true">

    <form method="post" action="<?php echo CONFIG['url']; ?>/login">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" placeholder="Username" value="admin" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" value="password" />
                    </div>
                    <div class="form-group">
                        <input name="remember" type="checkbox" class="form-check-input" id="remember-me" />
                        <label class="form-check-label" for="remember-me">Remember</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </div>
    </form>

</div>


<script src="<?php echo CONFIG['url']; ?>/assets/bootstrap.min.js"></script>



</body>
</html>

