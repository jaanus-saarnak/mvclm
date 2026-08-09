# MVCLM PHP Boilerplate

**PHP MVC framework shipped with a working user management application: authentication, access levels, an admin dashboard, and analytics.**

<div align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License: MIT"></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/MariaDB-MySQL-003545?style=for-the-badge&logo=mariadb&logoColor=white" alt="MariaDB / MySQL">
  <img src="https://img.shields.io/badge/Tailwind-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS v4">
  <img src="https://img.shields.io/badge/version-2.0-blue?style=for-the-badge" alt="Version 2.0">
</div>

<p align="center">
  <strong>Live demo: <a href="https://php.mvclm.com">php.mvclm.com</a></strong>
</p>

<p align="center">
  <strong>Application website: <a href="https://mvclm.com">mvclm.com</a></strong>
</p>

MVCLM PHP Boilerplate is a small MVC framework in plain PHP, shipped with the application most projects rebuild from scratch. Registration, sign-in, remembered sessions, access levels, an admin area for managing users, and an analytics page are all present and working, so a new project starts from a running application instead of an empty folder.

It is deliberately small enough to hold in your head, or in an AI assistant's context window. Seven classes in `Core/`, no service container, no configuration discovery, no code generation. Every request enters through one front controller, and the routing table is a single readable file.

## Stack

**Backend** - PHP 8.1 · mysqli · Composer (autoloader only, no packages)  
**Frontend** - Tailwind CSS v4 · jQuery 1.11.1 · Chart.js 4.5.1 · DataTables 1.13.7  
**Data** - MySQL or MariaDB  
**Server** - Apache with mod_rewrite  

Every library is served from this repository at a pinned version, so no script or stylesheet can change under the application without a commit here. The one external request is the Inter webfont, loaded from Google Fonts on every page. Everything still renders without it, since each layout falls back to the system font stack.

## Features

**Authentication**
- Registration with server-side validation and a live username availability check
- Sign-in against hashed passwords, with the session ID and the CSRF token both rotated on success
- Remember me: a database-backed access token held in a 30 day cookie
- Sign-out is POST only, and deletes the token row
- Wrong-password counter per IP address, with a configurable 24 hour limit
- Account self-delete, POST only, behind a confirmation
- Optional email verification: a new account confirms its address before it can sign in, with a single-use link that expires after 24 hours. Off unless `require_email_verification` is switched on
- Optional password reset: the "Forgot password?" link mails a single-use link that expires after an hour. The form answers the same way whether or not the address belongs to an account. Completing a reset also confirms the address and signs that account out everywhere. Off unless `enable_password_reset` is switched on

**Contact form**
- Name, address and message, mailed to whoever `contact_to_address` names. The visitor's address becomes the Reply-To, so the message can be answered by replying to it
- The subject line is written by the application rather than taken from the form

**Users and access levels**
- Three access levels: user, moderator, admin
- Four account statuses: active, inactive, suspended, pending
- Suspended and inactive accounts cannot sign in, and are signed out on their next request if an administrator changes the status while they are using the site
- Public user profile pages can optionally be restricted to signed-in visitors
- Suspended and inactive profiles answer 404, so their status stays private

**Admin**
- Users list with search, sorting, and pagination
- User edit page for access level and status. An administrator cannot demote themselves
- Analytics: growth and registration charts, status and access-level breakdowns, key metrics, recent activity

**Security**
- Every POST is checked for a CSRF token in the front controller, before routing
- Prepared-statement helpers on the database wrapper
- `src/` is denied to the web server by its own `.htaccess`, so classes, logs, and Composer files are never served
- The settings file, database password included, sits above the public root

**Operations and developer experience**
- `demo_mode` blocks registration, account deletion, access-level changes, password resets, and contact form submissions
- `show_errors` switches between the developer error page and the production 500 page
- Central error and exception handlers writing to a dated log
- Real 404 and 500 pages, and flash messages in four types, each with its own colour
- Two layouts, one public and one for signed-in pages
- Routed and styled placeholder pages for about, terms, privacy, and application settings, waiting for your content
- One script creates the database schema and seeds fifty four accounts
- Tailwind compiled into one committed stylesheet, so a clone needs no Node

## Project Structure

The repository root holds what a web server must not serve. The document root is `www/`.

```
mvclm/
├── env.example.php          # Settings template. Copy to env.php and fill in
├── migration.php            # Creates and seeds the database.
├── package.json             # Stylesheet build.
├── LICENSE
└── www/                     # Document root
    ├── .htaccess            # Sends every unmatched request to index.php
    ├── index.php            # Front controller: autoloader, CSRF gate
    ├── routes.php           # The routing table.
    ├── assets/
    │   ├── css/
    │   │   ├── app-source.css  # Stylesheet source. Edit this one
    │   │   └── app.css         # Generated by the Tailwind build. Do not edit
    │   ├── js/app.js
    │   ├── img/
    │   └── vendor/          # Third-party code: jQuery, Chart.js, DataTables
    └── src/                 # Denied to the web server by its own .htaccess
        ├── composer.json
        ├── Core/            # Controller, Model, Router, View, Csrf, Error, Mailer
        ├── App/
        │   ├── Controllers/
        │   ├── Models/
        │   ├── Views/
        │   │   └── layouts/ # main (public), dashboard (signed in)
        │   └── *.php        # Flash, AccountStatus, Contact, EmailVerification, PasswordReset
        └── logs/            # Dated error logs
```

## Requirements

- PHP 8.1 or newer, with the `mysqli` and `openssl` extensions
- MySQL or MariaDB
- Apache with `mod_rewrite` and `AllowOverride` enabled. Both the routing and the deny over `src/` are `.htaccess` rules, so neither works without it
- Composer

PHP 8.1 is a floor rather than a preference. The database wrapper relies on mysqli raising an exception when a call fails, which is the default from 8.1 onward.

## Installation

```bash
git clone https://github.com/jaanus-saarnak/mvclm.git
cd mvclm
composer install --working-dir=www/src
cp env.example.php env.php
```

`composer install` pulls no packages. It is there to generate the autoloader that `index.php` requires on every request, and a fresh clone has no `vendor/` folder.

Edit `env.php` with your URL and database credentials, create an empty database, then from the repository root:

```bash
php migration.php
```

Point the web server's document root at `www/`, and sign in as `admin` with the password `KHm2pX7MbZ`.

**`migration.php` is destructive and asks nothing first.** It drops each of its five tables before creating them, and it rebuilds whichever database `env.php` names.

**The administrator seeds with the password `KHm2pX7MbZ`, and the other fifty three accounts share the password `password`.** Both are in this repository, so neither is a secret. Change the administrator's, or reseed with your own data, before the site is reachable from the internet. `demo_mode` ships as `false`, which is what keeps those credentials off the sign-in page.

## Configuration

`env.php` is the only settings file. It sits beside `www/` rather than inside it, so the web server can never serve it, and it is untracked by design. Use `env.example.php` as template for it.

| key | what it does |
|---|---|
| `app_name` | Shown in the page title and the header |
| `url` | The application's own address, no trailing slash. Every redirect and form action is built from it |
| `db_host`, `db_name`, `db_user`, `db_password` | Database connection |
| `wrong_passwords_limit_from_ip` | Failed sign-ins from one IP address within 24 hours before it is blocked |
| `require_login_to_view_profile` | Hide profile pages from visitors who are not signed in |
| `show_errors` | Display errors as well as logging them. `false` on a live site |
| `demo_mode` | Block registration, account deletion, access-level changes, password resets, and contact form submissions |
| `require_email_verification` | Make a new account confirm its address before it can sign in. Needs the SMTP keys below |
| `enable_password_reset` | Offer a "Forgot password?" link that mails a reset link. Needs the SMTP keys below |
| `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password` | The relay outgoing mail is submitted to |
| `smtp_encryption` | `tls` to upgrade with STARTTLS, `ssl` to connect encrypted, empty for neither |
| `mail_from_address`, `mail_from_name` | Who outgoing mail comes from. The name is optional |
| `contact_to_address` | Where the contact form sends. Empty means the form is not shown |
| `path` | Where the `src` folder is. Change this only if you move it |

## Building the stylesheet

`www/assets/css/app.css` is generated and committed, so running the application needs no build step and no Node. Edit `www/assets/css/app-source.css` and rebuild only when you change the classes in the markup:

```bash
npm install
npm run build:css
```

## Worth knowing before you build on this

- **A new form needs a CSRF field.** Put `<?php echo \Core\Csrf::field(); ?>` inside it, or the front controller refuses the POST and it never reaches a controller. A script that posts sends `csrf_token`, from `window.MVCLM.csrfToken()`. Do not add a check of your own to a controller.
- **A route matches the path, never the method.** A state-changing route has to test for POST itself.
- **Routes are matched in order, and the profile route is registered last.** It accepts any single word as a username, so a route added after it is unreachable.
- **A view picks its layout by assigning `$layout`.** There is no argument and no setting. Leave it unset for the public layout.
- **`demo_mode` is not a read-only switch.** It guards five features by name: registration, account deletion, access-level changes, password reset, which is checked at both of its forms, and the contact form. The reset and contact pages stay visible so the features can still be seen, and only their submissions are refused. It cannot block writes in general, because signing in writes to the database.

## License

The MIT text below covers the code written for MVCLM. It does **not** cover the third-party code
under `www/assets/vendor/`, which keeps its own terms. Everything bundled there is MIT: jQuery,
Chart.js and DataTables, each with its licence file beside it. `www/assets/vendor/README.md`
lists versions, sources and the one modification made to a vendored file.

The Inter webfont is deliberately **not** bundled. It is under the SIL Open Font License rather
than MIT, and it is loaded from Google Fonts instead so that every file in this repository is
under one licence.

```
MIT License

Copyright (c) 2021 Jaanus Saarnak

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
