<?php
declare(strict_types=1);

/**
 * Double-submit CSRF tokens for POST requests (server-side only).
 */
function csrf_token(): string
{
    $cur = $_SESSION['_csrf_token'] ?? null;
    if (!is_string($cur) || $cur === '') {
        $cur = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $cur;
    }

    return $cur;
}

function csrf_field(): string
{
    $t = csrf_token();

    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify_post(): bool
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return false;
    }
    $sent = (string) ($_POST['_csrf'] ?? '');
    $exp = $_SESSION['_csrf_token'] ?? null;

    return is_string($exp) && $exp !== '' && $sent !== '' && hash_equals($exp, $sent);
}

/** Abort with 403 unless POST body contains a valid CSRF token. */
function csrf_require_post(): void
{
    if (!csrf_verify_post()) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        exit('Invalid or missing security token. Please reload the page and try again.');
    }
}

/** Issue a new token (e.g. after privilege change). */
function csrf_rotate(): void
{
    unset($_SESSION['_csrf_token']);
}
