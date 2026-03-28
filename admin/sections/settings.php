<?php
declare(strict_types=1);

$s = repo_settings();
?>
<div class="card">
  <h2>System configuration</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Values are stored in the MySQL <code style="color:var(--accent)">settings</code> table.
  </p>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/admin_settings_save.php') ?>" novalidate>
    <div class="form-row">
      <label for="company_name">Company name *</label>
      <input id="company_name" name="company_name" required maxlength="160" value="<?= e($s['company_name'] ?? '') ?>">
    </div>
    <div class="form-row">
      <label for="support_email">Support email *</label>
      <input id="support_email" name="support_email" type="email" required maxlength="120" value="<?= e($s['support_email'] ?? '') ?>">
    </div>
    <div class="form-row">
      <label for="default_region">Default service region</label>
      <input id="default_region" name="default_region" maxlength="160" value="<?= e($s['default_region'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn--primary">Save settings</button>
  </form>
</div>
