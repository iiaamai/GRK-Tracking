<?php
/**
 * Application configuration — swap mock storage for MySQL later (XAMPP).
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);

    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => $https,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

/** Base path on disk (project root). */
define('APP_ROOT', dirname(__DIR__));

/**
 * Minimal .env loader (KEY=VALUE lines).
 * Keeps deployment simple without extra dependencies.
 */
$envFile = APP_ROOT . DIRECTORY_SEPARATOR . '.env';
if (is_file($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $k = trim(substr($line, 0, $pos));
        $v = trim(substr($line, $pos + 1));
        if ($k === '') {
            continue;
        }

        // Strip optional surrounding quotes.
        if ((str_starts_with($v, '"') && str_ends_with($v, '"')) || (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
            $v = substr($v, 1, -1);
        }

        if (getenv($k) === false) {
            putenv($k . '=' . $v);
        }
        $_ENV[$k] = $_ENV[$k] ?? $v;
    }
}

/**
 * Web path prefix for generated links and redirects.
 * Use '' for URLs like /customer/login.php (site root = this project).
 * Use '/GRK-Tracking' only if the app is served from a subfolder of the web root
 * (e.g. default XAMPP http://localhost/GRK-Tracking/ with DocumentRoot = htdocs).
 */
define('BASE_URL', '');

/** MySQL (XAMPP default: user root, empty password). Used by data/db.php. */
define('DB_HOST', (string) (getenv('DB_HOST') !== false ? getenv('DB_HOST') : ''));
define('DB_NAME', (string) (getenv('DB_NAME') !== false ? getenv('DB_NAME') : ''));
define('DB_USER', (string) (getenv('DB_USER') !== false ? getenv('DB_USER') : ''));
define('DB_PASS', (string) (getenv('DB_PASS') !== false ? getenv('DB_PASS') : ''));
define('DB_CHARSET', 'utf8mb4');

/** AWS (S3 uploads). Prefer setting these in .env. */
define('AWS_REGION', (string) (getenv('AWS_REGION') !== false ? getenv('AWS_REGION') : ''));
define('AWS_S3_BUCKET', (string) (getenv('AWS_S3_BUCKET') !== false ? getenv('AWS_S3_BUCKET') : ''));
define('AWS_ACCESS_KEY_ID', (string) (getenv('AWS_ACCESS_KEY_ID') !== false ? getenv('AWS_ACCESS_KEY_ID') : ''));
define('AWS_SECRET_ACCESS_KEY', (string) (getenv('AWS_SECRET_ACCESS_KEY') !== false ? getenv('AWS_SECRET_ACCESS_KEY') : ''));
