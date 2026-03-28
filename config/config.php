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

/** Web path prefix (change if project folder name differs). */
define('BASE_URL', '/test-project');

/** MySQL placeholders — wire in phpMyAdmin / mysqli later. */
define('DB_HOST', 'localhost');
define('DB_NAME', 'express_logistics');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
