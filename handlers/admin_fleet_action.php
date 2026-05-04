<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_ADMIN);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
}

csrf_require_post();

$act = trim((string) ($_POST['action'] ?? ''));
$fleet = repo_fleet();

if ($act === 'add') {
    $label = trim((string) ($_POST['label'] ?? ''));
    $type = trim((string) ($_POST['type'] ?? ''));
    $plate = trim((string) ($_POST['plate'] ?? ''));
    $capacity_kg = (int) ($_POST['capacity_kg'] ?? 0);
    $status = trim((string) ($_POST['status'] ?? 'available'));
    if ($label === '' || $type === '' || $plate === '') {
        flash_set('error', 'Label, type, and plate are required.');
        redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
    }
    $fleet[] = [
        'id' => repo_next_fleet_id(),
        'label' => $label,
        'type' => $type,
        'plate' => $plate,
        'capacity_kg' => $capacity_kg,
        'status' => $status,
    ];
    repo_save_fleet($fleet);
    flash_set('success', 'Vehicle added to fleet.');
    redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Invalid vehicle.');
    redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
}

if ($act === 'edit') {
    $label = trim((string) ($_POST['label'] ?? ''));
    $type = trim((string) ($_POST['type'] ?? ''));
    $plate = trim((string) ($_POST['plate'] ?? ''));
    $capacity_kg = (int) ($_POST['capacity_kg'] ?? 0);
    $status = trim((string) ($_POST['status'] ?? 'available'));
    repo_update_fleet($id, static function (array $v) use ($label, $type, $plate, $capacity_kg, $status) {
        $v['label'] = $label;
        $v['type'] = $type;
        $v['plate'] = $plate;
        $v['capacity_kg'] = $capacity_kg;
        $v['status'] = $status;
        return $v;
    });
    flash_set('success', 'Vehicle updated.');
    redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
}

if ($act === 'delete') {
    repo_delete_fleet($id);
    flash_set('success', 'Vehicle removed.');
    redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
}

redirect(BASE_URL . '/admin/dashboard.php?section=fleet');
