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

?><!DOCTYPE html>
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <title>MvcLM Framework</title>
    <meta name="theme-color" content="#ffffff">

    <!-- CoreUI styles-->
    <link href="<?php echo CONFIG['url']; ?>/assets/css/simplebar.css" rel="stylesheet">
    <link href="<?php echo CONFIG['url']; ?>/assets/simplebar/css/simplebar.css" rel="stylesheet">
    <link href="<?php echo CONFIG['url']; ?>/assets/css/style.css" rel="stylesheet">

    <!-- MvcLM custom styles -->
    <link href="<?php echo CONFIG['url']; ?>/assets/css/custom.css" rel="stylesheet">

    <script src="<?php echo CONFIG['url']; ?>/assets/jquery/jquery-1.11.1.min.js"></script>
</head>

<body class="aside-menu-off-canvas">

    <div class="sidebar sidebar-fixed" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand px-4">
                <a class="mvclm-nav-link" href="<?php echo CONFIG['url']; ?>">App Name</a>
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
        </div>

        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo CONFIG['url']; ?>">
                    <svg class="nav-icon"><use xlink:href="<?php echo CONFIG['url']; ?>/assets/@coreui/icons/svg/free.svg#cil-home"></use></svg> Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo CONFIG['url']; ?>/search">
                    <svg class="nav-icon"><use xlink:href="<?php echo CONFIG['url']; ?>/assets/@coreui/icons/svg/free.svg#cil-search"></use></svg> Search
                </a>
            </li>
        </ul>
    </div>

    <div class="wrapper d-flex flex-column min-vh-100">

        <header class="header header-sticky p-0 mb-4">
            <div class="container-fluid px-5">

                <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
                    <svg class="icon icon-lg"><use xlink:href="<?php echo CONFIG['url']; ?>/assets/@coreui/icons/svg/free.svg#cil-menu"></use></svg>
                </button>

                <ul class="header-nav">
                    <?php
                    if (USER_LOGGED) {
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar avatar-md"><img class="avatar-img" src="<?php echo CONFIG['url']; ?>/assets/img/avatar.jpg" alt="Avatar"></div>
                                <svg class="avatar-triangle" xmlns="http://www.w3.org/2000/svg" width="0.7em" height="0.7em" viewBox="0 0 8 8"><path fill="currentColor" d="m0 2l4 4l4-4z"/></svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end pt-0">
                                <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Account</div>

                                <a class="dropdown-item" href="<?php echo CONFIG['url'] . '/' . LOGGED_USER_PROFILE['username']; ?>">
                                    <svg class="icon me-2"><use xlink:href="<?php echo CONFIG['url']; ?>/assets/@coreui/icons/svg/free.svg#cil-user"></use></svg> Profile
                                </a>

                                <a class="dropdown-item" href="<?php echo CONFIG['url']; ?>/settings">
                                    <svg class="icon me-2"><use xlink:href="<?php echo CONFIG['url']; ?>/assets/@coreui/icons/svg/free.svg#cil-settings"></use></svg> Settings
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="<?php echo CONFIG['url']; ?>/logout">
                                    <svg class="icon me-2"><use xlink:href="<?php echo CONFIG['url']; ?>/assets/@coreui/icons/svg/free.svg#cil-account-logout"></use></svg> Logout
                                </a>
                            </div>
                        </li>
                        <?php

                    } else {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link register-link" href="<?php echo CONFIG['url']; ?>/register">Register</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link login-link" href="<?php echo CONFIG['url']; ?>/login">Login</a>
                        </li>
                        <?php
                    }   ?>
                </ul>

            </div>
        </header>

<?php
if (isset($content['flash_messages']) && $content['flash_messages']) {

    foreach ($content['flash_messages'] as $flashMessage) {

        echo '<div class="alert alert-' . $flashMessage['type'] . '">';
        echo $flashMessage['body'] . '</div>';
    }

    ?><script>setTimeout(function() {$('.alert').fadeOut('slow');}, <?php echo $content['flash_messages'][0]['timer']; ?>);</script><?php
}

