<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=customers');
}

$act = trim((string) ($_POST['action'] ?? ''));
$rows = repo_customers();

if ($act === 'add') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    $password = (string) ($_POST['password'] ?? 'demo123');
    if ($username === '' || $name === '' || $email === '') {
        flash_set('error', 'Username, name, and email are required.');
        redirect(BASE_URL . '/admin/dashboard.php?section=customers');
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
        'password' => $password,
    ];
    repo_save_customers($rows);
    flash_set('success', 'Customer added.');
    redirect(BASE_URL . '/admin/dashboard.php?section=customers');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Invalid record.');
    redirect(BASE_URL . '/admin/dashboard.php?section=customers');
}

if ($act === 'edit') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    foreach ($rows as $i => $r) {
        if ((int) ($r['id'] ?? 0) === $id) {
            $rows[$i]['name'] = $name;
            $rows[$i]['email'] = $email;
            $rows[$i]['mobile'] = $mobile;
            break;
        }
    }
    repo_save_customers($rows);
    flash_set('success', 'Customer updated.');
    redirect(BASE_URL . '/admin/dashboard.php?section=customers');
}

if ($act === 'delete') {
    $rows = array_values(array_filter($rows, static fn ($r) => (int) ($r['id'] ?? 0) !== $id));
    repo_save_customers($rows);
    flash_set('success', 'Customer removed from list.');
    redirect(BASE_URL . '/admin/dashboard.php?section=customers');
}

redirect(BASE_URL . '/admin/dashboard.php?section=customers');
