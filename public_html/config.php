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

/**
 * App configurations
 */
$appConfig = [
    'url' => 'https://www.mvclm.com/demo',
    'path' => 'src',
    'app_name' => 'MVC Framework',
    'show_errors' => false,
    'admin_uid' => 1,
    'wrong_passwords_limit_from_ip' => 100,
    'demo_mode' => false,
    'require_login_to_view_profile' => false,
    'db_host' => 'localhost',
    'db_name' => '',
    'db_user' => '',
    'db_password' => ''
];

define("CONFIG", $appConfig);

