<?php
declare(strict_types=1);

$period = (string) ($_GET['period'] ?? 'daily');
$period = in_array($period, ['daily', 'weekly', 'monthly', 'yearly'], true) ? $period : 'daily';

$tz = new DateTimeZone('Asia/Manila');
$now = new DateTimeImmutable('now', $tz);

// Anchor date/year (optional)
$date = trim((string) ($_GET['date'] ?? ''));
$year = (int) ($_GET['year'] ?? 0);
if ($date !== '') {
    $date = preg_replace('/[^0-9-]+/', '', $date);
    try {
        $anchor = new DateTimeImmutable($date, $tz);
    } catch (Throwable $e) {
        $anchor = $now;
    }
} elseif ($year > 0) {
    $anchor = new DateTimeImmutable(sprintf('%04d-01-01', $year), $tz);
} else {
    $anchor = $now;
}

$start = $anchor;
$end = $anchor;

if ($period === 'weekly') {
    // ISO week, Monday..Sunday
    $start = $anchor->modify('monday this week');
    $end = $anchor->modify('sunday this week');
} elseif ($period === 'monthly') {
    $start = $anchor->modify('first day of this month');
    $end = $anchor->modify('last day of this month');
} elseif ($period === 'yearly') {
    $start = $anchor->setDate((int) $anchor->format('Y'), 1, 1);
    $end = $anchor->setDate((int) $anchor->format('Y'), 12, 31);
}

$sum = repo_admin_earnings_summary_for_range($start->format('Y-m-d'), $end->format('Y-m-d'));

$base = 'dashboard.php?section=earnings_reports';
$dateParam = $anchor->format('Y-m-d');
$tab = static function (string $p, string $label) use ($base, $period, $dateParam): void {
    $href = $base . '&period=' . urlencode($p) . '&date=' . urlencode($dateParam);
    $cls = $period === $p ? 'is-active' : '';
    echo '<a class="tab ' . e($cls) . '" href="' . e($href) . '">' . e($label) . '</a>';
};
?>

<div class="card">
  <h2>Earnings & Reports</h2>
  <p style="margin:0;color:var(--muted);font-size:0.9rem;">
    Calculated from <strong>completed</strong> deliveries within the selected date range.
  </p>
</div>

<div class="card">
  <div class="tabs" role="tablist" aria-label="Earnings periods">
    <?php $tab('daily', 'Daily'); ?>
    <?php $tab('weekly', 'Weekly'); ?>
    <?php $tab('monthly', 'Monthly'); ?>
    <?php $tab('yearly', 'Yearly'); ?>
  </div>

  <form method="get" action="./dashboard.php" style="margin:1rem 0 0;display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">
    <input type="hidden" name="section" value="earnings_reports">
    <input type="hidden" name="period" value="<?= e($period) ?>">
    <div class="form-row" style="margin:0;min-width:220px">
      <label for="date">Anchor date</label>
      <input id="date" name="date" type="date" value="<?= e($anchor->format('Y-m-d')) ?>">
    </div>
    <button type="submit" class="btn btn--ghost">Apply</button>
  </form>

  <?php
    $csvHref = '../handlers/admin_export_earnings_csv.php'
      . '?period=' . urlencode($period)
      . '&date=' . urlencode($anchor->format('Y-m-d'));
  ?>
  <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center">
    <a class="btn btn--ghost" href="<?= e($csvHref) ?>">Export CSV</a>
    <small style="color:var(--muted)">Exports the filtered completed deliveries for the selected range.</small>
  </div>
</div>

<div class="grid grid--stats">
  <div class="stat">
    <div class="stat__val"><?= e(format_php_money((float) ($sum['total_earnings'] ?? 0))) ?></div>
    <div class="stat__lbl">Total earnings</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= (int) ($sum['total_bookings'] ?? 0) ?></div>
    <div class="stat__lbl">Completed deliveries</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= (int) ($sum['total_maintenance_vehicles'] ?? 0) ?></div>
    <div class="stat__lbl">Vehicles under maintenance</div>
  </div>
</div>

<div class="card">
  <h2>Date range</h2>
  <p style="margin:0;color:var(--muted)">
    <?= e((string) ($sum['date_range_start'] ?? '')) ?> → <?= e((string) ($sum['date_range_end'] ?? '')) ?>
  </p>
</div>

