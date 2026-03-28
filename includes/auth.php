<?php
declare(strict_types=1);

require_once APP_ROOT . '/data/repository.php';

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

function auth_require_role(string $role): void
{
    $u = auth_user();
    if (!$u || ($u['role'] ?? '') !== $role) {
        flash_set('error', 'Please sign in to continue.');
        $map = [
            'customer' => BASE_URL . '/customer/login.php',
            'driver' => BASE_URL . '/driver/login.php',
            'admin' => BASE_URL . '/admin/login.php',
        ];
        redirect($map[$role] ?? BASE_URL . '/index.php');
    }
}

function auth_logout(): void
{
    $_SESSION['user'] = null;
    unset($_SESSION['user']);
}
