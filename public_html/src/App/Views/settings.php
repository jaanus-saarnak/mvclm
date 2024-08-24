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
            <div class="card">
                <div class="card-body">

                    <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                        <div class="col-auto">
                            <h5>Settings</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12"><hr></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 px-3">
                            <a onclick="return confirm('DELETE USER ACCOUNT ?')" class="btn btn-outline-secondary me-2" href="<?php echo CONFIG['url']; ?>/delete/<?php echo LOGGED_USER_PROFILE['uid']; ?>">Delete account</a>
                            This action can not be undone
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once(CONFIG['path'] . '/App/Views/footer.php');

