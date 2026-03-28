<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=settings');
}

$company_name = trim((string) ($_POST['company_name'] ?? ''));
$support_email = trim((string) ($_POST['support_email'] ?? ''));
$default_region = trim((string) ($_POST['default_region'] ?? ''));

if ($company_name === '' || $support_email === '') {
    flash_set('error', 'Company name and support email are required.');
    redirect(BASE_URL . '/admin/dashboard.php?section=settings');
}

repo_save_settings([
    'company_name' => $company_name,
    'support_email' => $support_email,
    'default_region' => $default_region,
]);

flash_set('success', 'Settings saved.');
redirect(BASE_URL . '/admin/dashboard.php?section=settings');
