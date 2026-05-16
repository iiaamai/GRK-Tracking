<?php
declare(strict_types=1);

/** Password reset token lifetime (seconds). */
const PW_RESET_TTL = 900;

/**
 * @param 'customer'|'driver' $role
 */
function pw_reset_issue(string $role, array $userRow): string
{
    $token = bin2hex(random_bytes(32));
    if (!isset($_SESSION['pw_reset']) || !is_array($_SESSION['pw_reset'])) {
        $_SESSION['pw_reset'] = [];
    }
    $_SESSION['pw_reset'][$role] = [
        'user_id' => (int) ($userRow['id'] ?? 0),
        'username' => (string) ($userRow['username'] ?? ''),
        'email' => (string) ($userRow['email'] ?? ''),
        'token' => $token,
        'expires_at' => time() + PW_RESET_TTL,
    ];

    return $token;
}

/**
 * @param 'customer'|'driver' $role
 * @return array{user_id:int,username:string,email:string,token:string,expires_at:int}|null
 */
function pw_reset_get(string $role): ?array
{
    $data = $_SESSION['pw_reset'][$role] ?? null;
    if (!is_array($data)) {
        return null;
    }

    return $data;
}

/**
 * @param 'customer'|'driver' $role
 */
function pw_reset_validate(string $role, string $token): bool
{
    $data = pw_reset_get($role);
    if ($data === null) {
        return false;
    }
    if (($data['expires_at'] ?? 0) < time()) {
        return false;
    }
    $stored = (string) ($data['token'] ?? '');

    return $stored !== '' && hash_equals($stored, $token);
}

/**
 * @param 'customer'|'driver' $role
 */
function pw_reset_clear(string $role): void
{
    if (isset($_SESSION['pw_reset'][$role])) {
        unset($_SESSION['pw_reset'][$role]);
    }
}

/**
 * Build reset URL for simulated email page.
 *
 * @param 'customer'|'driver' $portal
 */
function pw_reset_url(string $portal, string $token): string
{
    $path = match ($portal) {
        'customer' => '/customer/reset_password.php',
        'driver' => '/driver/reset_password.php',
        default => '/index.php',
    };

    return rtrim(BASE_URL, '/') . $path . '?token=' . rawurlencode($token);
}
