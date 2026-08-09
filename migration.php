<?php
/**
 * Installs the demo database and fills it with sample accounts.
 *
 * Run this once after cloning, or whenever the database needs rebuilding. It
 * creates the users, sessions, login_ip, email_verifications and password_resets
 * tables and seeds one admin, three moderators and fifty users. The
 * administrator's password is 'KHm2pX7MbZ' and every other seeded account has
 * 'password'. Both are printed in the report it writes, and the login page
 * prints them too when demo_mode is on. Every seeded account is created already
 * verified.
 *
 * It is destructive. Each table is dropped before it is created, so running it
 * against a database holding real accounts erases them. There is no prompt.
 *
 * This file sits at the repo root, beside www/ rather than inside it, so it is
 * never reachable over HTTP. Run it from a shell. The settings are loaded
 * through __DIR__ so that works from whatever folder you are in.
 *
 * It reports in whichever format fits where it is running: plain text under the
 * command line, a styled HTML page in a browser. Every mig_ function below
 * decides that once, so the code doing the work never mentions markup.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

require_once __DIR__ . '/env.php';

/** True when running under the command line, which is the only thing the output format depends on. */
define('MIG_CLI', PHP_SAPI === 'cli');

/**
 * Open the report.
 */
function mig_head()
{
    $title = 'MVCLM Database Migration';

    if (MIG_CLI) {
        echo "\n" . $title . "\n" . str_repeat('=', strlen($title)) . "\n";
        return;
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVCLM Database Migration</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #6366f1;
            margin-bottom: 30px;
            text-align: center;
        }
        .section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .section h2 {
            color: #4f46e5;
            font-size: 1.2em;
            margin: 0 0 15px 0;
        }
        .success {
            color: #10b981;
            font-weight: 500;
        }
        .error {
            color: #ef4444;
            font-weight: 500;
        }
        .info {
            color: #6b7280;
            margin: 5px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-label {
            font-size: 0.875em;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 1.5em;
            font-weight: 600;
            color: #111827;
        }
        .credentials {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .sample-users {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        .user-item {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75em;
            font-weight: 500;
            margin-left: 8px;
        }
        .badge-admin { background: #fee2e2; color: #991b1b; }
        .badge-moderator { background: #fef3c7; color: #92400e; }
        .badge-user { background: #dbeafe; color: #1e40af; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #f3f4f6; color: #374151; }
        .badge-suspended { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .progress {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MVCLM Database Migration</h1>
    <?php
}

/**
 * Close the report, shutting whichever section is still open.
 */
function mig_foot()
{
    mig_section_close();

    if (MIG_CLI) {
        echo "\n";
        return;
    }
    echo "    </div>\n</body>\n</html>\n";
}

/**
 * Close the open section, if there is one.
 *
 * Sections close themselves so that no caller has to pair an opening call with
 * a closing one, which is the mistake this file would otherwise invite.
 */
function mig_section_close()
{
    if (!MIG_CLI && mig_section_open()) {
        echo "        </div>\n";
    }
    mig_section_open(false);
}

/**
 * Read or set whether a section is currently open.
 */
function mig_section_open($set = null)
{
    static $open = false;

    if ($set !== null) {
        $open = $set;
    }
    return $open;
}

/**
 * Start a section. The style names the CSS class the browser version uses and
 * has no effect on the command line.
 */
function mig_section($title, $style = 'section')
{
    mig_section_close();
    mig_section_open(true);

    if (MIG_CLI) {
        echo "\n" . $title . "\n" . str_repeat('-', strlen($title)) . "\n";
        return;
    }
    echo '        <div class="' . $style . '">' . "\n";
    echo '            <h2>' . htmlspecialchars($title) . "</h2>\n";
}

/**
 * Write one line. The kind is 'ok', 'fail' or 'info'.
 */
function mig_line($text, $kind = 'info')
{
    if (MIG_CLI) {
        $marks = ['ok' => '  ✓ ', 'fail' => '  ✗ ', 'info' => '    '];
        echo $marks[$kind] . $text . "\n";
        return;
    }
    $classes = ['ok' => 'success', 'fail' => 'error', 'info' => 'info'];
    $prefix = ['ok' => '&#10003; ', 'fail' => '&#10007; ', 'info' => ''];
    echo '            <p class="' . $classes[$kind] . '">' . $prefix[$kind] . htmlspecialchars($text) . "</p>\n";
}

/**
 * Write the counts. Each row is [label, value].
 *
 * The whole list arrives at once so the browser version can wrap it in its grid
 * and the command line version can line the numbers up.
 */
function mig_stats(array $rows)
{
    if (MIG_CLI) {
        $width = 0;
        foreach ($rows as $row) {
            $width = max($width, strlen($row[0]));
        }
        foreach ($rows as $row) {
            echo '    ' . str_pad($row[0] . ' ', $width + 4, '.') . ' ' . $row[1] . "\n";
        }
        return;
    }
    echo '            <div class="stats">' . "\n";
    foreach ($rows as $row) {
        echo '                <div class="stat-box">';
        echo '<div class="stat-label">' . htmlspecialchars($row[0]) . '</div>';
        echo '<div class="stat-value">' . htmlspecialchars($row[1]) . '</div>';
        echo "</div>\n";
    }
    echo "            </div>\n";
}

/**
 * Write the sample accounts. Each row carries username, access_level and status.
 */
function mig_items(array $rows)
{
    if (MIG_CLI) {
        $width = 0;
        foreach ($rows as $row) {
            $width = max($width, strlen($row['username']));
        }
        foreach ($rows as $row) {
            echo '    ' . str_pad($row['username'], $width + 3)
                . str_pad($row['access_level'], 12)
                . $row['status'] . "\n";
        }
        return;
    }
    foreach ($rows as $row) {
        echo '            <div class="user-item">' . htmlspecialchars($row['username']);
        echo '<span class="badge badge-' . $row['access_level'] . '">' . htmlspecialchars($row['access_level']) . '</span>';
        echo '<span class="badge badge-' . $row['status'] . '">' . htmlspecialchars($row['status']) . '</span>';
        echo "</div>\n";
    }
}

/**
 * Open the row of progress dots.
 */
function mig_progress_start()
{
    echo MIG_CLI ? '    ' : '            <div class="progress">';
}

/**
 * Add one dot and push it out, so a long seed shows it is still working.
 */
function mig_tick()
{
    echo MIG_CLI ? '.' : '<span class="info">.</span>';
    flush();
}

/**
 * Close the row of progress dots.
 */
function mig_progress_end()
{
    echo MIG_CLI ? "\n" : "</div>\n";
}

/**
 * Report a failure that cannot be recovered from, close the report and stop.
 */
function mig_stop($text)
{
    mig_line($text, 'fail');
    mig_foot();
    exit(1);
}

mig_head();

mig_section('Database Connection');

$mysqli = new mysqli(
    CONFIG['db_host'],
    CONFIG['db_user'],
    CONFIG['db_password'],
    CONFIG['db_name']
);

if ($mysqli->connect_errno) {
    mig_stop('Connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

mig_line('Connected to database successfully', 'ok');
mig_line('Database: ' . CONFIG['db_name']);
mig_line('Host: ' . CONFIG['db_host']);

mig_section('Creating Database Tables');

// Create users table
$mysqli->query("DROP TABLE IF EXISTS `users`");
$result = $mysqli->query("
    CREATE TABLE `users` (
        `uid` int(20) NOT NULL AUTO_INCREMENT,
        `username` varchar(16) NOT NULL,
        `password_hash` varchar(200) NOT NULL,
        `email` varchar(200) NOT NULL,
        `email_verified_at` datetime DEFAULT NULL,
        `access_level` enum('user','moderator','admin') NOT NULL DEFAULT 'user',
        `registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `last_login_ip` varchar(20) DEFAULT NULL,
        `logins_cookie_stats` varchar(2000) DEFAULT NULL,
        `status` enum('active','inactive','suspended','pending') NOT NULL DEFAULT 'active',
        PRIMARY KEY (`uid`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`),
        KEY `idx_access_level` (`access_level`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$result
    ? mig_line('Users table created', 'ok')
    : mig_line('Error creating users table: ' . $mysqli->error, 'fail');

// Create sessions table
$mysqli->query("DROP TABLE IF EXISTS `sessions`");
$result = $mysqli->query("
    CREATE TABLE `sessions` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `uid` bigint(20) NOT NULL,
        `accesstoken` varchar(200) NOT NULL,
        `created` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_uid` (`uid`),
        KEY `idx_created` (`created`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$result
    ? mig_line('Sessions table created', 'ok')
    : mig_line('Error creating sessions table: ' . $mysqli->error, 'fail');

// Create login_ip table
$mysqli->query("DROP TABLE IF EXISTS `login_ip`");
$result = $mysqli->query("
    CREATE TABLE `login_ip` (
        `id` int(20) NOT NULL AUTO_INCREMENT,
        `uid` int(20) DEFAULT NULL,
        `ip` varchar(100) NOT NULL,
        `time_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `password` enum('correct','wrong') NOT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_ip` (`ip`),
        KEY `idx_time` (`time_added`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$result
    ? mig_line('Login IP table created', 'ok')
    : mig_line('Error creating login_ip table: ' . $mysqli->error, 'fail');

// Create email_verifications table
$mysqli->query("DROP TABLE IF EXISTS `email_verifications`");
$result = $mysqli->query("
    CREATE TABLE `email_verifications` (
        `id` int(20) NOT NULL AUTO_INCREMENT,
        `uid` int(20) NOT NULL,
        `token_hash` char(64) NOT NULL,
        `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `expires` datetime NOT NULL,
        `used` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `token_hash` (`token_hash`),
        KEY `idx_uid` (`uid`),
        KEY `idx_expires` (`expires`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$result
    ? mig_line('Email verifications table created', 'ok')
    : mig_line('Error creating email_verifications table: ' . $mysqli->error, 'fail');

// Create password_resets table
$mysqli->query("DROP TABLE IF EXISTS `password_resets`");
$result = $mysqli->query("
    CREATE TABLE `password_resets` (
        `id` int(20) NOT NULL AUTO_INCREMENT,
        `uid` int(20) NOT NULL,
        `token_hash` char(64) NOT NULL,
        `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `expires` datetime NOT NULL,
        `used` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `token_hash` (`token_hash`),
        KEY `idx_uid` (`uid`),
        KEY `idx_expires` (`expires`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$result
    ? mig_line('Password resets table created', 'ok')
    : mig_line('Error creating password_resets table: ' . $mysqli->error, 'fail');

mig_section('Generating Sample Data');

$firstNames = ['James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Charles',
              'Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen',
              'Christopher', 'Daniel', 'Matthew', 'Anthony', 'Donald', 'Mark', 'Paul', 'Steven', 'Andrew', 'Kenneth',
              'Nancy', 'Betty', 'Helen', 'Sandra', 'Donna', 'Carol', 'Ruth', 'Sharon', 'Michelle', 'Laura'];

$lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
             'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
             'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson'];

$domains = ['example.com'];
$passwordHash = password_hash('password', PASSWORD_DEFAULT);

// The administrator has its own password
$adminPasswordHash = password_hash('KHm2pX7MbZ', PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("
    INSERT INTO `users` (`username`, `password_hash`, `email`, `email_verified_at`, `access_level`, `registered`, `last_login`, `last_login_ip`, `status`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Create admin user
$username = 'admin';
$email = 'admin@example.com';
$accessLevel = 'admin';
$registered = date('Y-m-d H:i:s', strtotime('-1 year'));
$lastLogin = date('Y-m-d H:i:s', strtotime('-1 day'));
$ip = '127.0.0.1';
$status = 'active';
$emailVerifiedAt = $registered;

$stmt->bind_param('sssssssss', $username, $adminPasswordHash, $email, $emailVerifiedAt, $accessLevel, $registered, $lastLogin, $ip, $status);
$stmt->execute();
mig_line('Admin user created', 'ok');

// Create moderator users
for ($i = 1; $i <= 3; $i++) {
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $username = strtolower($firstName) . '_mod' . $i;
    $email = strtolower($firstName . '.' . $lastName . $i) . '@' . $domains[array_rand($domains)];
    $accessLevel = 'moderator';
    $registered = date('Y-m-d H:i:s', strtotime('-' . rand(30, 300) . ' days'));
    $lastLogin = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
    $ip = rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    $status = 'active';
    $emailVerifiedAt = $registered;

    $stmt->bind_param('sssssssss', $username, $passwordHash, $email, $emailVerifiedAt, $accessLevel, $registered, $lastLogin, $ip, $status);
    $stmt->execute();
}
mig_line('3 moderator users created', 'ok');

// Create regular users
$userCount = 50;
$createdCount = 0;

mig_progress_start();
for ($i = 1; $i <= $userCount; $i++) {
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $username = strtolower($firstName) . rand(100, 999);
    $email = strtolower($firstName . '.' . $lastName . rand(10, 99)) . '@' . $domains[array_rand($domains)];
    $accessLevel = 'user';
    $registered = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days'));
    $lastLogin = date('Y-m-d H:i:s', strtotime('-' . rand(0, 90) . ' days'));
    $ip = rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);

    $random = rand(1, 100);
    if ($random <= 75) {
        $status = 'active';
    } elseif ($random <= 85) {
        $status = 'inactive';
    } elseif ($random <= 90) {
        $status = 'suspended';
    } else {
        $status = 'pending';
    }

    $emailVerifiedAt = $registered;

    $stmt->bind_param('sssssssss', $username, $passwordHash, $email, $emailVerifiedAt, $accessLevel, $registered, $lastLogin, $ip, $status);

    if (!$stmt->execute()) {
        $i--;
        continue;
    }

    $createdCount++;
    if ($createdCount % 10 == 0) {
        mig_tick();
    }
}
mig_progress_end();
mig_line($createdCount . ' regular users created', 'ok');

$stmt->close();

mig_section('Database Statistics');

$stats = [];

$result = $mysqli->query("SELECT access_level, COUNT(*) as count FROM users GROUP BY access_level");
while ($row = $result->fetch_assoc()) {
    $stats[] = [ucfirst($row['access_level']) . 's', $row['count']];
}

$result = $mysqli->query("SELECT status, COUNT(*) as count FROM users GROUP BY status");
while ($row = $result->fetch_assoc()) {
    $stats[] = [ucfirst($row['status']) . ' Users', $row['count']];
}

$result = $mysqli->query("SELECT COUNT(*) as total FROM users");
$row = $result->fetch_assoc();
$stats[] = ['Total Users', $row['total']];

mig_stats($stats);

mig_section('Login Credentials', 'credentials');
mig_line('Username: admin');
mig_line('Password: KHm2pX7MbZ');
mig_line("Every other seeded account has the password 'password'.");

mig_section('Sample Users', 'sample-users');

$users = [];
$result = $mysqli->query("SELECT username, access_level, status FROM users WHERE access_level != 'admin' ORDER BY RAND() LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
mig_items($users);

$mysqli->close();

mig_foot();
