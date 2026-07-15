<?php
/**
 * hub.niche — View a single audit report
 */

session_start();
$NICHE_NAV_ACTIVE = 'history';
require_once __DIR__ . '/config.php';
$BASE = HUBNICHE_DATA_DIR;

$slug = $_GET['slug'] ?? '';
$id   = $_GET['id'] ?? '';

$data = null;

// Fast path: slug given directly
if ($slug !== '' && file_exists($BASE . '/' . $slug . '/audit.json')) {
    $data = json_decode(file_get_contents($BASE . '/' . $slug . '/audit.json'), true);
}

// Fallback: scan all project folders for matching id
if (!$data && $id !== '') {
    foreach (glob($BASE . '/*/audit.json') as $file) {
        $tmp = json_decode(file_get_contents($file), true);
        if (($tmp['id'] ?? '') === $id) { $data = $tmp; break; }
    }
}

if (!$data) {
    die('Audit not found. <a href="history.php">Back to history</a>');
}

function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES); }

$r = $data['result'];
$ratingLabel = $r['rating']['label'] ?? '';
$pillClass = 'pill-red';
if (str_contains($ratingLabel, 'Strong')) $pillClass = 'pill-green';
elseif (str_contains($ratingLabel, 'Workable')) $pillClass = 'pill-yellow';
elseif (str_contains($ratingLabel, 'Marginal')) $pillClass = 'pill-orange';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Audit Report — <?= h($data['niche']) ?> / <?= h($data['city']) ?></title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5f6fa; margin: 0; color: #1e1e2e; }
  .wrap { max-width: 820px; margin: 0 auto; padding: 32px 20px; }
  .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }
  h1 { font-size: 22px; margin-bottom: 4px; }
  .sub { color: #666; margin-bottom: 20px; font-size: 14px; }
  .scorebar { display:flex; gap:16px; margin: 16px 0; }
  .scorebox { flex:1; background:#f8f9ff; border-radius:8px; padding:16px; text-align:center; }
  .scorebox .big { font-size:26px; font-weight:800; }
  .scorebox .lbl { font-size:12px; color:#888; margin-top:4px; }
  .pill { display: inline-block; padding: 5px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; }
  .pill-green { background: #dcfce7; color: #166534; }
  .pill-yellow { background: #fef9c3; color: #854d0e; }
  .pill-orange { background: #ffedd5; color: #9a3412; }
  .pill-red { background: #fee2e2; color: #991b1b; }
  ul.rec { padding-left: 20px; }
  ul.rec li { margin-bottom: 8px; font-size: 14px; }
  table { width:100%; border-collapse: collapse; font-size: 13px; margin-top: 10px; }
  th, td { text-align:left; padding: 8px; border-bottom: 1px solid #eee; }
  a.back { color:#4f46e5; text-decoration:none; font-weight:600; font-size: 13px; }
</style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="wrap">
  <a class="back" href="history.php">← Back to history</a>
  &nbsp;·&nbsp;
  <a class="back" href="audit-form.php?state=<?= urlencode($data['state'] ?? '') ?>&city=<?= urlencode($data['city'] ?? '') ?>&population=<?= urlencode($data['population'] ?? '') ?>">← Back to keyword page</a>
  <h1 style="margin-top:14px;">📊 <?= h($data['niche']) ?> — <?= h($data['city']) ?><?= !empty($data['state']) ? ', ' . h($data['state']) : '' ?></h1>
  <div class="sub">Population: <?= number_format($data['population']) ?> &nbsp;|&nbsp; Keyword: <?= h($data['keyword']) ?> &nbsp;|&nbsp; Audited <?= h(date('M j, Y g:ia', strtotime($data['created_at']))) ?></div>

  <div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      <div style="font-size:40px; font-weight:800;"><?= $r['total'] ?>%</div>
      <span class="pill <?= $pillClass ?>"><?= $r['rating']['emoji'] ?> <?= h($ratingLabel) ?></span>
    </div>
    <div class="scorebar">
      <div class="scorebox"><div class="big"><?= h($data['niche_score']['label']) ?></div><div class="lbl">NICHE FIT (30% weight)</div></div>
      <div class="scorebox"><div class="big"><?= h($data['city_score']['label']) ?></div><div class="lbl">CITY FIT (20% weight)</div></div>
      <div class="scorebox">
        <div class="big"><?= isset($r['competition_pct']) ? $r['competition_pct'] . '%' : '—' ?></div>
        <div class="lbl"><?= isset($r['competition_pct']) ? 'COMPETITION WEAKNESS (50% weight)' : 'COMPETITION — NOT MEASURED' ?></div>
      </div>
    </div>
  </div>

  <div class="card">
    <h3 style="margin-top:0;">🚨 Recommendations</h3>
    <ul class="rec">
      <?php foreach ($data['recommendations'] as $rec): ?>
        <li><?= h($rec) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="card">
    <h3 style="margin-top:0;">Niche Fit Detail</h3>
    <p><?= h($data['niche_score']['note']) ?></p>
    <h3>City Fit Detail</h3>
    <p><?= h($data['city_score']['note']) ?></p>
  </div>

  <?php if (!empty($data['competitor_scores'])): ?>
  <div class="card">
    <h3 style="margin-top:0;">Top 3 Competitor Breakdown</h3>
    <table>
      <tr><th>Domain</th><th>Weakness Score</th><th>Signals</th></tr>
      <?php foreach ($data['competitor_scores'] as $c): ?>
        <tr>
          <td><?= h($c['domain']) ?></td>
          <td><?= $c['score'] ?>/<?= $c['max'] ?></td>
          <td><?= h(implode('; ', $c['notes'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
