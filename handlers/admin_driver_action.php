<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

$act = trim((string) ($_POST['action'] ?? ''));
$rows = repo_drivers();

if ($act === 'add') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    $vehicle_type = trim((string) ($_POST['vehicle_type'] ?? ''));
    $plate = trim((string) ($_POST['plate'] ?? ''));
    $capacity_kg = (int) ($_POST['capacity_kg'] ?? 0);
    $password = (string) ($_POST['password'] ?? 'demo123');
    if ($username === '' || $name === '' || $email === '' || $vehicle_type === '' || $plate === '') {
        flash_set('error', 'Fill required driver fields.');
        redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
    }
    $nextId = 1;
    foreach ($rows as $r) {
        $nextId = max($nextId, (int) ($r['id'] ?? 0) + 1);
    }
    $rows[] = [
        'id' => $nextId,
        'username' => $username,
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'vehicle_type' => $vehicle_type,
        'plate' => $plate,
        'capacity_kg' => $capacity_kg,
        'password' => $password,
    ];
    repo_save_drivers($rows);
    flash_set('success', 'Driver added.');
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Invalid record.');
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

if ($act === 'edit') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    $vehicle_type = trim((string) ($_POST['vehicle_type'] ?? ''));
    $plate = trim((string) ($_POST['plate'] ?? ''));
    $capacity_kg = (int) ($_POST['capacity_kg'] ?? 0);
    foreach ($rows as $i => $r) {
        if ((int) ($r['id'] ?? 0) === $id) {
            $rows[$i]['name'] = $name;
            $rows[$i]['email'] = $email;
            $rows[$i]['mobile'] = $mobile;
            $rows[$i]['vehicle_type'] = $vehicle_type;
            $rows[$i]['plate'] = $plate;
            $rows[$i]['capacity_kg'] = $capacity_kg;
            break;
        }
    }
    repo_save_drivers($rows);
    flash_set('success', 'Driver updated.');
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

if ($act === 'delete') {
    $rows = array_values(array_filter($rows, static fn ($r) => (int) ($r['id'] ?? 0) !== $id));
    repo_save_drivers($rows);
    flash_set('success', 'Driver removed from list.');
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
