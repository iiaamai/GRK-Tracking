<?php
declare(strict_types=1);

require_once APP_ROOT . '/data/mock_data.php';

/**
 * Session-backed store until MySQL is connected.
 * TODO: replace with mysqli/PDO prepared statements using same function signatures.
 */

function repo_init(): void
{
    if (!isset($_SESSION['_repo'])) {
        $mu = mock_users();
        $_SESSION['_repo'] = [
            'bookings' => mock_bookings_seed(),
            'fleet' => mock_fleet_seed(),
            'customers' => $mu['customers'],
            'drivers' => $mu['drivers'],
            'settings' => [
                'company_name' => 'Express Urban Logistics',
                'support_email' => 'support@express.test',
                'default_region' => 'NCR + Calabarzon',
            ],
            'booking_seq' => 3,
            'fleet_seq' => 6,
        ];
    } else {
        if (!isset($_SESSION['_repo']['settings'])) {
            $_SESSION['_repo']['settings'] = [
                'company_name' => 'Express Urban Logistics',
                'support_email' => 'support@express.test',
                'default_region' => 'NCR + Calabarzon',
            ];
        }
        if (!isset($_SESSION['_repo']['customers'])) {
            $_SESSION['_repo']['customers'] = mock_users()['customers'];
        }
        if (!isset($_SESSION['_repo']['drivers'])) {
            $_SESSION['_repo']['drivers'] = mock_users()['drivers'];
        }
    }
}

function repo_bookings(): array
{
    repo_init();
    return $_SESSION['_repo']['bookings'];
}

function repo_save_bookings(array $bookings): void
{
    repo_init();
    $_SESSION['_repo']['bookings'] = $bookings;
}

function repo_fleet(): array
{
    repo_init();
    return $_SESSION['_repo']['fleet'];
}

function repo_save_fleet(array $fleet): void
{
    repo_init();
    $_SESSION['_repo']['fleet'] = $fleet;
}

function repo_next_booking_number(): string
{
    repo_init();
    $n = (int) $_SESSION['_repo']['booking_seq'];
    $_SESSION['_repo']['booking_seq'] = $n + 1;
    return sprintf('EXP-2026-%04d', $n);
}

function repo_add_booking(array $row): void
{
    $all = repo_bookings();
    $all[] = $row;
    repo_save_bookings($all);
}

function repo_update_booking(string $bookingNumber, callable $fn): void
{
    $all = repo_bookings();
    foreach ($all as $i => $b) {
        if (($b['booking_number'] ?? '') === $bookingNumber) {
            $all[$i] = $fn($b);
            break;
        }
    }
    repo_save_bookings($all);
}

function repo_find_booking_by_number(string $num): ?array
{
    foreach (repo_bookings() as $b) {
        if (strcasecmp($b['booking_number'] ?? '', $num) === 0) {
            return $b;
        }
    }
    return null;
}

function repo_find_bookings_by_username(string $user): array
{
    $u = strtolower(trim($user));
    $out = [];
    foreach (repo_bookings() as $b) {
        if (strtolower((string) ($b['username'] ?? '')) === $u) {
            $out[] = $b;
        }
    }
    return $out;
}

function repo_customer_bookings(int $customerId): array
{
    $out = [];
    foreach (repo_bookings() as $b) {
        if ((int) ($b['customer_id'] ?? 0) === $customerId) {
            $out[] = $b;
        }
    }
    return $out;
}

function repo_driver_jobs_available(): array
{
    $out = [];
    foreach (repo_bookings() as $b) {
        if (($b['status'] ?? '') === 'pending') {
            $out[] = $b;
        }
    }
    return $out;
}

function repo_driver_deliveries(int $driverId): array
{
    $out = [];
    foreach (repo_bookings() as $b) {
        if ((int) ($b['driver_id'] ?? 0) === $driverId && in_array($b['status'] ?? '', ['assigned', 'in_transit'], true)) {
            $out[] = $b;
        }
    }
    return $out;
}

function repo_driver_earnings(int $driverId): float
{
    $sum = 0.0;
    foreach (repo_bookings() as $b) {
        if ((int) ($b['driver_id'] ?? 0) === $driverId && ($b['status'] ?? '') === 'completed') {
            $sum += (float) ($b['payout'] ?? 4500);
        }
    }
    return $sum;
}

function repo_add_fleet_row(array $row): void
{
    repo_init();
    $fleet = repo_fleet();
    $fleet[] = $row;
    repo_save_fleet($fleet);
}

function repo_update_fleet(int $id, callable $fn): void
{
    $fleet = repo_fleet();
    foreach ($fleet as $i => $v) {
        if ((int) ($v['id'] ?? 0) === $id) {
            $fleet[$i] = $fn($v);
            break;
        }
    }
    repo_save_fleet($fleet);
}

function repo_delete_fleet(int $id): void
{
    $fleet = array_values(array_filter(repo_fleet(), static fn ($v) => (int) ($v['id'] ?? 0) !== $id));
    repo_save_fleet($fleet);
}

function repo_customers(): array
{
    repo_init();
    return $_SESSION['_repo']['customers'];
}

function repo_save_customers(array $rows): void
{
    repo_init();
    $_SESSION['_repo']['customers'] = $rows;
}

function repo_drivers(): array
{
    repo_init();
    return $_SESSION['_repo']['drivers'];
}

function repo_save_drivers(array $rows): void
{
    repo_init();
    $_SESSION['_repo']['drivers'] = $rows;
}

function repo_settings(): array
{
    repo_init();
    return $_SESSION['_repo']['settings'];
}

function repo_save_settings(array $s): void
{
    repo_init();
    $_SESSION['_repo']['settings'] = array_merge(repo_settings(), $s);
}

function repo_next_fleet_id(): int
{
    repo_init();
    $id = (int) $_SESSION['_repo']['fleet_seq'];
    $_SESSION['_repo']['fleet_seq'] = $id + 1;
    return $id;
}

function repo_next_customer_id(): int
{
    $nextId = 1;
    foreach (repo_customers() as $r) {
        $nextId = max($nextId, (int) ($r['id'] ?? 0) + 1);
    }
    return $nextId;
}

function repo_next_driver_id(): int
{
    $nextId = 1;
    foreach (repo_drivers() as $r) {
        $nextId = max($nextId, (int) ($r['id'] ?? 0) + 1);
    }
    return $nextId;
}

function repo_customer_username_exists(string $username): bool
{
    $u = strtolower(trim($username));
    foreach (repo_customers() as $r) {
        if (strtolower((string) ($r['username'] ?? '')) === $u) {
            return true;
        }
    }
    return false;
}

function repo_driver_username_exists(string $username): bool
{
    $u = strtolower(trim($username));
    foreach (repo_drivers() as $r) {
        if (strtolower((string) ($r['username'] ?? '')) === $u) {
            return true;
        }
    }
    return false;
}

function repo_driver_by_id(int $id): ?array
{
    foreach (repo_drivers() as $r) {
        if ((int) ($r['id'] ?? 0) === $id) {
            return $r;
        }
    }
    return null;
}
