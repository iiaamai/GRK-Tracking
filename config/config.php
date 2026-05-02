<?php
/**
 * Application configuration — swap mock storage for MySQL later (XAMPP).
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

/** Base path on disk (project root). */
define('APP_ROOT', dirname(__DIR__));

/**
 * Web path prefix for generated links and redirects.
 * Use '' for URLs like /customer/login.php (site root = this project).
 * Use '/GRK-Tracking' only if the app is served from a subfolder of the web root
 * (e.g. default XAMPP http://localhost/GRK-Tracking/ with DocumentRoot = htdocs).
 */
define('BASE_URL', '');

/** MySQL (XAMPP default: user root, empty password). Used by data/db.php. */
define('DB_HOST', 'localhost');
define('DB_NAME', 'express_logistics');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
