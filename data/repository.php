<?php
declare(strict_types=1);

require_once APP_ROOT . '/data/db.php';

/**
 * MySQL-backed store (PDO). Schema: data/schema.sql
 */

function repo_init(): void
{
    // No-op; kept for compatibility with any code that called repo_init().
}

function repo_map_booking_row(array $row): array
{
    $row['customer_id'] = (int) $row['customer_id'];
    $row['driver_id'] = isset($row['driver_id']) && $row['driver_id'] !== null && $row['driver_id'] !== ''
        ? (int) $row['driver_id'] : null;
    $row['payout'] = isset($row['payout']) && $row['payout'] !== null && $row['payout'] !== ''
        ? (float) $row['payout'] : null;

    return $row;
}

function repo_map_customer_row(array $row): array
{
    $row['id'] = (int) $row['id'];

    return $row;
}

function repo_map_driver_row(array $row): array
{
    $row['id'] = (int) $row['id'];
    $row['capacity_kg'] = (int) $row['capacity_kg'];

    return $row;
}

function repo_map_fleet_row(array $row): array
{
    $row['id'] = (int) $row['id'];
    $row['capacity_kg'] = (int) $row['capacity_kg'];

    return $row;
}

function repo_bookings(): array
{
    $pdo = db();
    $stmt = $pdo->query(
        'SELECT * FROM bookings ORDER BY booking_datetime DESC, id DESC'
    );
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_booking_row($row);
    }

    return $out;
}

function repo_save_bookings(array $bookings): void
{
    $pdo = db();
    $nums = [];
    foreach ($bookings as $b) {
        if (($b['booking_number'] ?? '') !== '') {
            $nums[] = (string) $b['booking_number'];
        }
    }
    $pdo->beginTransaction();
    try {
        if ($nums === []) {
            $pdo->exec('DELETE FROM bookings');
        } else {
            $ph = implode(',', array_fill(0, count($nums), '?'));
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_number NOT IN ($ph)");
            $stmt->execute($nums);
        }
        foreach ($bookings as $b) {
            repo_upsert_booking_row($b);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function repo_upsert_booking_row(array $b): void
{
    $pdo = db();
    $sql = <<<'SQL'
INSERT INTO bookings (
  booking_number, customer_id, username, name, email, mobile,
  booking_datetime, posting_date, vehicle_type, pickup, dropoff,
  cargo_desc, additional_requirements, status, driver_id, payout
) VALUES (
  ?,?,?,?,?,?,
  ?,?,?,?,?,
  ?,?,?,?,?
) ON DUPLICATE KEY UPDATE
  customer_id = VALUES(customer_id),
  username = VALUES(username),
  name = VALUES(name),
  email = VALUES(email),
  mobile = VALUES(mobile),
  booking_datetime = VALUES(booking_datetime),
  posting_date = VALUES(posting_date),
  vehicle_type = VALUES(vehicle_type),
  pickup = VALUES(pickup),
  dropoff = VALUES(dropoff),
  cargo_desc = VALUES(cargo_desc),
  additional_requirements = VALUES(additional_requirements),
  status = VALUES(status),
  driver_id = VALUES(driver_id),
  payout = VALUES(payout)
SQL;
    $stmt = $pdo->prepare($sql);
    $driverId = $b['driver_id'] ?? null;
    $payout = $b['payout'] ?? null;
    $stmt->execute([
        $b['booking_number'] ?? '',
        (int) ($b['customer_id'] ?? 0),
        (string) ($b['username'] ?? ''),
        (string) ($b['name'] ?? ''),
        (string) ($b['email'] ?? ''),
        (string) ($b['mobile'] ?? ''),
        (string) ($b['booking_datetime'] ?? ''),
        (string) ($b['posting_date'] ?? ''),
        (string) ($b['vehicle_type'] ?? ''),
        (string) ($b['pickup'] ?? ''),
        (string) ($b['dropoff'] ?? ''),
        (string) ($b['cargo_desc'] ?? ''),
        (string) ($b['additional_requirements'] ?? ''),
        (string) ($b['status'] ?? 'pending'),
        $driverId !== null ? (int) $driverId : null,
        $payout !== null ? (string) $payout : null,
    ]);
}

function repo_fleet(): array
{
    $pdo = db();
    $stmt = $pdo->query('SELECT * FROM fleet ORDER BY id ASC');
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_fleet_row($row);
    }

    return $out;
}

function repo_save_fleet(array $fleet): void
{
    $pdo = db();
    $ids = [];
    foreach ($fleet as $v) {
        if (isset($v['id'])) {
            $ids[] = (int) $v['id'];
        }
    }
    $pdo->beginTransaction();
    try {
        if ($ids === []) {
            $pdo->exec('DELETE FROM fleet');
        } else {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM fleet WHERE id NOT IN ($ph)");
            $stmt->execute($ids);
        }
        foreach ($fleet as $v) {
            repo_upsert_fleet_row($v);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function repo_upsert_fleet_row(array $v): void
{
    $pdo = db();
    $sql = <<<'SQL'
INSERT INTO fleet (id, label, type, plate, capacity_kg, status)
VALUES (?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
  label = VALUES(label),
  type = VALUES(type),
  plate = VALUES(plate),
  capacity_kg = VALUES(capacity_kg),
  status = VALUES(status)
SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        (int) ($v['id'] ?? 0),
        (string) ($v['label'] ?? ''),
        (string) ($v['type'] ?? ''),
        (string) ($v['plate'] ?? ''),
        (int) ($v['capacity_kg'] ?? 0),
        (string) ($v['status'] ?? 'available'),
    ]);
}

function repo_next_booking_number(): string
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'booking_seq' FOR UPDATE");
        $n = (int) $stmt->fetchColumn();
        $upd = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'booking_seq'");
        $upd->execute([(string) ($n + 1)]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return sprintf('EXP-2026-%04d', $n);
}

function repo_add_booking(array $row): void
{
    repo_upsert_booking_row($row);
}

function repo_update_booking(string $bookingNumber, callable $fn): void
{
    $current = repo_find_booking_by_number($bookingNumber);
    if ($current === null) {
        return;
    }
    $updated = $fn($current);
    repo_upsert_booking_row($updated);
}

function repo_find_booking_by_number(string $num): ?array
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM bookings WHERE booking_number = ? LIMIT 1');
    $stmt->execute([$num]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    return repo_map_booking_row($row);
}

function repo_find_bookings_by_username(string $user): array
{
    $u = strtolower(trim($user));
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM bookings WHERE LOWER(username) = ? ORDER BY booking_datetime DESC');
    $stmt->execute([$u]);
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_booking_row($row);
    }

    return $out;
}

function repo_customer_bookings(int $customerId): array
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM bookings WHERE customer_id = ? ORDER BY booking_datetime DESC');
    $stmt->execute([$customerId]);
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_booking_row($row);
    }

    return $out;
}

function repo_driver_jobs_available(): array
{
    $pdo = db();
    $stmt = $pdo->query("SELECT * FROM bookings WHERE status = 'pending' ORDER BY booking_datetime ASC");
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_booking_row($row);
    }

    return $out;
}

function repo_driver_deliveries(int $driverId): array
{
    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT * FROM bookings WHERE driver_id = ? AND status IN ('assigned','in_transit') ORDER BY booking_datetime ASC"
    );
    $stmt->execute([$driverId]);
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_booking_row($row);
    }

    return $out;
}

function repo_driver_earnings(int $driverId): float
{
    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(payout), 0) FROM bookings WHERE driver_id = ? AND status = 'completed'"
    );
    $stmt->execute([$driverId]);

    return (float) $stmt->fetchColumn();
}

function repo_add_fleet_row(array $row): void
{
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
    $pdo = db();
    $stmt = $pdo->query('SELECT * FROM customers ORDER BY id ASC');
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_customer_row($row);
    }

    return $out;
}

function repo_upsert_customer_row(array $r): void
{
    $pdo = db();
    $sql = <<<'SQL'
INSERT INTO customers (id, username, email, password, name, mobile)
VALUES (?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
  username = VALUES(username),
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name),
  mobile = VALUES(mobile)
SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        (int) ($r['id'] ?? 0),
        (string) ($r['username'] ?? ''),
        (string) ($r['email'] ?? ''),
        (string) ($r['password'] ?? ''),
        (string) ($r['name'] ?? ''),
        (string) ($r['mobile'] ?? ''),
    ]);
}

function repo_save_customers(array $rows): void
{
    if ($rows === []) {
        return;
    }
    $pdo = db();
    $ids = [];
    foreach ($rows as $r) {
        if (isset($r['id'])) {
            $ids[] = (int) $r['id'];
        }
    }
    $pdo->beginTransaction();
    try {
        foreach ($rows as $r) {
            repo_upsert_customer_row($r);
        }
        if ($ids !== []) {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $sql = "DELETE FROM customers WHERE id NOT IN ($ph)
              AND NOT EXISTS (SELECT 1 FROM bookings b WHERE b.customer_id = customers.id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($ids);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function repo_insert_customer(string $username, string $email, string $password, string $name, string $mobile): void
{
    $pdo = db();
    $stmt = $pdo->prepare(
        'INSERT INTO customers (username, email, password, name, mobile) VALUES (?,?,?,?,?)'
    );
    $stmt->execute([$username, $email, $password, $name, $mobile]);
}

function repo_update_customer_profile(int $id, string $name, string $email, string $mobile): void
{
    $pdo = db();
    $stmt = $pdo->prepare('UPDATE customers SET name = ?, email = ?, mobile = ? WHERE id = ?');
    $stmt->execute([$name, $email, $mobile, $id]);
}

function repo_drivers(): array
{
    $pdo = db();
    $stmt = $pdo->query('SELECT * FROM drivers ORDER BY id ASC');
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $out[] = repo_map_driver_row($row);
    }

    return $out;
}

function repo_upsert_driver_row(array $r): void
{
    $pdo = db();
    $sql = <<<'SQL'
INSERT INTO drivers (id, username, email, password, name, mobile, vehicle_type, plate, capacity_kg)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
  username = VALUES(username),
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name),
  mobile = VALUES(mobile),
  vehicle_type = VALUES(vehicle_type),
  plate = VALUES(plate),
  capacity_kg = VALUES(capacity_kg)
SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        (int) ($r['id'] ?? 0),
        (string) ($r['username'] ?? ''),
        (string) ($r['email'] ?? ''),
        (string) ($r['password'] ?? ''),
        (string) ($r['name'] ?? ''),
        (string) ($r['mobile'] ?? ''),
        (string) ($r['vehicle_type'] ?? ''),
        (string) ($r['plate'] ?? ''),
        (int) ($r['capacity_kg'] ?? 0),
    ]);
}

function repo_save_drivers(array $rows): void
{
    if ($rows === []) {
        return;
    }
    $pdo = db();
    $ids = [];
    foreach ($rows as $r) {
        if (isset($r['id'])) {
            $ids[] = (int) $r['id'];
        }
    }
    $pdo->beginTransaction();
    try {
        foreach ($rows as $r) {
            repo_upsert_driver_row($r);
        }
        if ($ids !== []) {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $sql = "DELETE FROM drivers WHERE id NOT IN ($ph)
              AND NOT EXISTS (SELECT 1 FROM bookings b WHERE b.driver_id = drivers.id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($ids);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function repo_insert_driver(
    string $username,
    string $email,
    string $password,
    string $name,
    string $mobile,
    string $vehicle_type,
    string $plate,
    int $capacity_kg
): void {
    $pdo = db();
    $stmt = $pdo->prepare(
        'INSERT INTO drivers (username, email, password, name, mobile, vehicle_type, plate, capacity_kg)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([$username, $email, $password, $name, $mobile, $vehicle_type, $plate, $capacity_kg]);
}

function repo_update_driver_profile(
    int $id,
    string $name,
    string $email,
    string $mobile,
    string $vehicle_type,
    string $plate,
    int $capacity_kg
): void {
    $pdo = db();
    $stmt = $pdo->prepare(
        'UPDATE drivers SET name = ?, email = ?, mobile = ?, vehicle_type = ?, plate = ?, capacity_kg = ? WHERE id = ?'
    );
    $stmt->execute([$name, $email, $mobile, $vehicle_type, $plate, $capacity_kg, $id]);
}

function repo_settings(): array
{
    $pdo = db();
    $stmt = $pdo->query('SELECT setting_key, setting_value FROM settings');
    $pairs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    return is_array($pairs) ? $pairs : [];
}

function repo_save_settings(array $s): void
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        foreach ($s as $key => $value) {
            $stmt->execute([(string) $key, (string) $value]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function repo_next_fleet_id(): int
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'fleet_seq' FOR UPDATE");
        $n = (int) $stmt->fetchColumn();
        $upd = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'fleet_seq'");
        $upd->execute([(string) ($n + 1)]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return $n;
}

function repo_next_customer_id(): int
{
    return (int) db()->query('SELECT COALESCE(MAX(id), 0) + 1 FROM customers')->fetchColumn();
}

function repo_next_driver_id(): int
{
    return (int) db()->query('SELECT COALESCE(MAX(id), 0) + 1 FROM drivers')->fetchColumn();
}

function repo_customer_username_exists(string $username): bool
{
    $u = strtolower(trim($username));
    $stmt = db()->prepare('SELECT 1 FROM customers WHERE LOWER(username) = ? LIMIT 1');
    $stmt->execute([$u]);

    return (bool) $stmt->fetchColumn();
}

function repo_driver_username_exists(string $username): bool
{
    $u = strtolower(trim($username));
    $stmt = db()->prepare('SELECT 1 FROM drivers WHERE LOWER(username) = ? LIMIT 1');
    $stmt->execute([$u]);

    return (bool) $stmt->fetchColumn();
}

function repo_driver_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM drivers WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    return repo_map_driver_row($row);
}
