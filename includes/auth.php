<?php
declare(strict_types=1);

require_once APP_ROOT . '/data/repository.php';

/** @var string Role slug stored in session */
const AUTH_ROLE_CUSTOMER = 'customer';
const AUTH_ROLE_DRIVER = 'driver';
const AUTH_ROLE_ADMIN = 'admin';

function auth_login(string $role, string $username, string $password): ?array
{
    if ($role === 'customer') {
        foreach (repo_customers() as $u) {
            if (strcasecmp((string) ($u['username'] ?? ''), $username) === 0 && ($u['password'] ?? '') === $password) {
                return $u + ['role' => 'customer'];
            }
        }
        return null;
    }
    if ($role === 'driver') {
        foreach (repo_drivers() as $u) {
            if (strcasecmp((string) ($u['username'] ?? ''), $username) === 0 && ($u['password'] ?? '') === $password) {
                return $u + ['role' => 'driver'];
            }
        }
        return null;
    }
    if ($role === 'admin') {
        $stmt = db()->prepare('SELECT id, username, email, password, name FROM admins WHERE LOWER(username) = LOWER(?) LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if ($row && ($row['password'] ?? '') === $password) {
            return $row + ['role' => 'admin'];
        }
        return null;
    }
    return null;
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_login_url(string $role): string
{
    return match ($role) {
        AUTH_ROLE_CUSTOMER => BASE_URL . '/customer/login.php',
        AUTH_ROLE_DRIVER => BASE_URL . '/driver/login.php',
        AUTH_ROLE_ADMIN => BASE_URL . '/admin/login.php',
        default => BASE_URL . '/index.php',
    };
}

/** Default home URL for a role (used after login or when blocking cross-portal access). */
function auth_dashboard_url(string $role): string
{
    return match ($role) {
        AUTH_ROLE_CUSTOMER => BASE_URL . '/customer/dashboard.php',
        AUTH_ROLE_DRIVER => BASE_URL . '/driver/dashboard.php',
        AUTH_ROLE_ADMIN => BASE_URL . '/admin/dashboard.php',
        default => BASE_URL . '/index.php',
    };
}

/**
 * Regenerate session ID after authentication changes (mitigates fixation).
 */
function auth_session_regenerate(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Require an authenticated user with the given role.
 *
 * Options:
 * - forbidden_wrong_role: if true, authenticated users with a different role get HTTP 403
 *   (use for downloads/API-like handlers). If false, they are redirected to their own portal.
 */
function auth_require_role(string $role, array $options = []): void
{
    $forbiddenWrong = !empty($options['forbidden_wrong_role']);
    $u = auth_user();
    if (!$u) {
        flash_set('error', 'Please sign in to continue.');
        redirect(auth_login_url($role));
    }
    if (($u['role'] ?? '') !== $role) {
        if ($forbiddenWrong) {
            http_response_code(403);
            header('Content-Type: text/plain; charset=UTF-8');
            exit('Forbidden');
        }
        flash_set('error', 'You do not have access to that area.');
        redirect(auth_dashboard_url((string) ($u['role'] ?? '')));
    }
}

/** Require login as one of the given roles (first match used for guest redirect target). */
function auth_require_any_role(array $roles, array $options = []): void
{
    $roles = array_values(array_filter($roles, static fn ($r) => is_string($r) && $r !== ''));
    if ($roles === []) {
        throw new InvalidArgumentException('auth_require_any_role: empty roles');
    }
    $u = auth_user();
    if (!$u) {
        flash_set('error', 'Please sign in to continue.');
        redirect(auth_login_url($roles[0]));
    }
    $have = (string) ($u['role'] ?? '');
    if (!in_array($have, $roles, true)) {
        if (!empty($options['forbidden_wrong_role'])) {
            http_response_code(403);
            header('Content-Type: text/plain; charset=UTF-8');
            exit('Forbidden');
        }
        flash_set('error', 'You do not have access to that area.');
        redirect(auth_dashboard_url($have));
    }
}

function auth_logout(): void
{
    $_SESSION['user'] = null;
    unset($_SESSION['user']);
}
