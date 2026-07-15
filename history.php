<?php
/**
 * hub.niche — History Dashboard
 */

session_start();
$NICHE_NAV_ACTIVE = 'history';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/niche-criteria.php';

$BASE = HUBNICHE_DATA_DIR;
if (!is_dir($BASE)) { @mkdir($BASE, 0755, true); }

$audits = [];
foreach (glob($BASE . '/*/audit.json') as $file) {
    $data = json_decode(file_get_contents($file), true);
    if ($data) { $audits[] = $data; }
}
usort($audits, fn($a, $b) => strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Audit History — hub.niche</title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5f6fa; margin: 0; color: #1e1e2e; }
  .wrap { max-width: 960px; margin: 0 auto; padding: 32px 20px; }
  h1 { font-size: 22px; margin-bottom: 4px; }
  .sub { color: #666; margin-bottom: 24px; font-size: 14px; }
  .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 24px; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #eee; }
  th { color: #888; font-weight: 600; text-transform: uppercase; font-size: 11px; }
  .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
  .pill-green { background: #dcfce7; color: #166534; }
  .pill-yellow { background: #fef9c3; color: #854d0e; }
  .pill-orange { background: #ffedd5; color: #9a3412; }
  .pill-red { background: #fee2e2; color: #991b1b; }
  a.link { color: #4f46e5; text-decoration: none; font-weight: 600; }
  .btn { background: #4f46e5; color: #fff; border: none; padding: 11px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration:none; display:inline-block; }
  .btn:hover { background: #4338ca; }
  .empty { color:#999; padding: 20px 0; }
</style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="wrap">
  <h1>📊 Audit History</h1>
  <div class="sub">All past niche/city opportunity audits, newest first.</div>

  <a href="research.php" class="btn">+ New Audit</a>

  <div class="card" style="margin-top:20px;">
    <?php if (empty($audits)): ?>
      <div class="empty">
        No audits yet — click "+ New Audit" above to run your first one.<br>
        <span style="font-size:13px;">First time here? <a href="index.php" style="color:#c8871c; font-weight:600;">Read the intro & setup guide →</a></span>
      </div>
    <?php else: ?>
    <table>
      <tr><th>Date</th><th>Niche</th><th>Location</th><th>Score</th><th>Rating</th><th></th></tr>
      <?php foreach ($audits as $a): ?>
        <?php
          $r = $a['result']['rating']['label'] ?? '';
          $pillClass = 'pill-red';
          if (str_contains($r, 'Strong')) $pillClass = 'pill-green';
          elseif (str_contains($r, 'Workable')) $pillClass = 'pill-yellow';
          elseif (str_contains($r, 'Marginal')) $pillClass = 'pill-orange';
          $location = htmlspecialchars($a['city'] ?? '') . (!empty($a['state']) ? ', ' . htmlspecialchars($a['state']) : '');
        ?>
        <tr>
          <td><?= htmlspecialchars(date('M j, Y', strtotime($a['created_at'] ?? 'now'))) ?></td>
          <td><?= htmlspecialchars($a['niche'] ?? '') ?></td>
          <td><?= $location ?></td>
          <td><strong><?= $a['result']['total'] ?? '—' ?>%</strong></td>
          <td><span class="pill <?= $pillClass ?>"><?= htmlspecialchars($r) ?></span></td>
          <td><a class="link" href="view-audit.php?id=<?= urlencode($a['id']) ?>">View →</a></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
