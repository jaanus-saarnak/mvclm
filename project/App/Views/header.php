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

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="<?php echo CONFIG['url']; ?>/assets/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo CONFIG['url']; ?>/assets/overwrites.css">
<title><?php echo CONFIG['app_name']; ?></title>
<meta name="description" content="As simple as possible - Login Manager" />
<meta name="keywords" content="login manager" />

<script src="<?php echo CONFIG['url']; ?>/assets/jquery.min.js"></script>

</head>
<body>

<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal"><a class="page-title" href="<?php echo CONFIG['url']; ?>"><?php echo CONFIG['app_name']; ?></a></h5>

    <nav class="my-2 my-md-0 mr-md-3">
        <a class="p-2 text-dark" href="<?php echo CONFIG['url']; ?>">Main Page</a>
        <a class="p-2 text-dark" href="<?php echo CONFIG['url']; ?>/search">Search</a>

    <?php
    if (USER_LOGGED) {

        echo '</nav>';

        echo '<a class="btn btn-outline-primary" href="' . CONFIG['url'] . '/logout">Logout</a>';

    } else {

        echo '<a class="p-2 text-dark" href="' . CONFIG['url'] . '/register">Register</a>';

        echo '</nav>';

        echo '<a class="btn btn-primary" data-toggle="modal" data-target="#loginModal" href="#">Login</a>';
    }
    ?>

</div>

<?php
if (isset($content['flash_messages']) && $content['flash_messages']) {

    foreach ($content['flash_messages'] as $flashMessage) {

        echo '<div class="alert alert-margin alert-' . $flashMessage['type'] . '">';

        echo $flashMessage['body'] . '</div>';
    }

    ?><script>setTimeout(function() {$('.alert').fadeOut('fast');}, <?php echo $content['flash_messages'][0]['timer']; ?>);</script><?php
}

