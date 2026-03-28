<?php
declare(strict_types=1);

$jobs = repo_driver_jobs_available();
?>
<div class="card">
  <h2>Pending requests</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Accept a job to assign it to you and move it to <strong>My Deliveries</strong>.
  </p>
  <?php if (!$jobs): ?>
    <p style="color:var(--muted)">No open jobs. When customers book, listings appear here.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Pickup time</th>
            <th>Route</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($jobs as $b): ?>
            <tr>
              <td><?= e($b['booking_number'] ?? '') ?></td>
              <td><?= e($b['name'] ?? '') ?></td>
              <td><?= e($b['vehicle_type'] ?? '') ?></td>
              <td><?= e($b['booking_datetime'] ?? '') ?></td>
              <td><?= e($b['pickup'] ?? '') ?> → <?= e($b['dropoff'] ?? '') ?></td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/driver_accept_job.php') ?>" style="margin:0">
                  <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                  <button type="submit" class="btn btn--primary" style="padding:0.35rem 0.65rem;font-size:0.85rem">Accept</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
