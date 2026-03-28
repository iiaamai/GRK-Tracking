<?php
declare(strict_types=1);

$u = auth_user();
?>
<div class="card">
  <h2>Personal & contact</h2>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/profile_customer.php') ?>" novalidate>
    <div class="grid grid--2">
      <div class="form-row">
        <label for="name">Display name *</label>
        <input id="name" name="name" required maxlength="120" value="<?= e((string) $u['name']) ?>">
      </div>
      <div class="form-row">
        <label for="username">Username</label>
        <input id="username" name="username" value="<?= e((string) $u['username']) ?>" disabled>
      </div>
      <div class="form-row">
        <label for="email">Email *</label>
        <input id="email" name="email" type="email" required value="<?= e((string) $u['email']) ?>">
      </div>
      <div class="form-row">
        <label for="mobile">Mobile *</label>
        <input id="mobile" name="mobile" required value="<?= e((string) $u['mobile']) ?>">
      </div>
    </div>
    <button type="submit" class="btn btn--primary">Save profile</button>
  </form>
  <p style="margin-top:1rem;font-size:0.85rem;color:var(--muted)">
    Username is fixed for login; wire MySQL later to allow admin-approved changes.
  </p>
</div>
