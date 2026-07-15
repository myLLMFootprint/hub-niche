<?php
require_once __DIR__ . '/config.php';
$NICHE_NAV_ACTIVE = 'settings';

$saved = false;
$cleared = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_key'])) {
        if (file_exists(HUBNICHE_SETTINGS_FILE)) @unlink(HUBNICHE_SETTINGS_FILE);
        $cleared = true;
    } else {
        $key = trim($_POST['ahrefs_key'] ?? '');
        file_put_contents(HUBNICHE_SETTINGS_FILE, json_encode(['ahrefs_key' => $key], JSON_PRETTY_PRINT));
        $saved = true;
    }
}

$currentKey = hubniche_ahrefs_key();
$fromConfig = ($currentKey !== '' && $currentKey === HUBNICHE_AHREFS_KEY && !file_exists(HUBNICHE_SETTINGS_FILE));

// Count saved audits
$auditCount = is_dir(HUBNICHE_DATA_DIR) ? count(glob(HUBNICHE_DATA_DIR . '/*/audit.json')) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings — hub.niche</title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5f6fa; margin: 0; color: #1e1e2e; }
  .wrap { max-width: 760px; margin: 0 auto; padding: 32px 20px; }
  h1 { font-size: 22px; margin-bottom: 4px; }
  .sub { color: #666; font-size: 14px; margin-bottom: 24px; }
  .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }
  .eyebrow { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #c8871c; font-weight: 700; margin-bottom: 8px; }
  label { display: block; font-size: 13px; font-weight: 600; margin: 12px 0 5px; }
  input[type=password], input[type=text] { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
  .btn { background: #14213d; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
  .btn:hover { background: #1f2f52; }
  .btn-ghost { background: #eef0f4; color: #14213d; }
  .note { font-size: 12px; color: #777; margin-top: 8px; }
  .ok { background: #e2f0e7; color: #166534; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
  .status { font-size: 13px; padding: 8px 12px; border-radius: 6px; display: inline-block; }
  .status.on { background: #e2f0e7; color: #166534; }
  .status.off { background: #eef0f4; color: #555; }
  code { background: #f0efea; padding: 1px 6px; border-radius: 4px; font-size: 12px; }
  .divider { height: 1px; background: #eee; margin: 20px 0; }
</style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="wrap">
  <h1>Settings</h1>
  <div class="sub">hub.niche works fully without any setup. The options here are optional.</div>

  <?php if ($saved): ?><div class="ok">✓ Settings saved.</div><?php endif; ?>
  <?php if ($cleared): ?><div class="ok">✓ Ahrefs key removed. Falling back to the config file value (if any).</div><?php endif; ?>

  <div class="card">
    <div class="eyebrow">Competitor data</div>
    <p style="font-size:14px; margin-top:4px;">
      <strong>Default (no key):</strong> the audit form gives you one-click buttons to the free Ahrefs
      checker and Google. You read the numbers and type them in. No account, no cost.
    </p>
    <div class="divider"></div>
    <p style="font-size:14px;">
      Current status:
      <?php if ($currentKey !== ''): ?>
        <span class="status on">Ahrefs API key is set<?= $fromConfig ? ' (from config.php)' : ' (from this page)' ?></span>
      <?php else: ?>
        <span class="status off">No API key — using the free button workflow</span>
      <?php endif; ?>
    </p>

    <form method="POST">
      <label>Ahrefs API key <span style="font-weight:400;color:#999;">optional — for auto-fill, requires a paid Ahrefs API plan</span></label>
      <input type="password" name="ahrefs_key" placeholder="Paste your Ahrefs API token" value="<?= htmlspecialchars(file_exists(HUBNICHE_SETTINGS_FILE) ? $currentKey : '') ?>">
      <div class="note">
        Saved to <code>settings.json</code> on your server and used only for requests to Ahrefs. This overrides the
        value in <code>config.php</code>. Leave blank and save to fall back to the config file.
      </div>
      <div style="margin-top:14px; display:flex; gap:8px;">
        <button type="submit" class="btn">Save key</button>
        <button type="submit" name="clear_key" value="1" class="btn btn-ghost">Remove key</button>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="eyebrow">Your data</div>
    <p style="font-size:14px; margin-top:4px;">
      <strong><?= $auditCount ?></strong> audit<?= $auditCount === 1 ? '' : 's' ?> saved so far, stored as JSON files
      under <code>projects/</code> on your server. Unlike a browser-only tool, this history persists across devices
      and browsers as long as the files are on the server.
    </p>
  </div>

  <div class="card">
    <div class="eyebrow">Support</div>
    <p style="font-size:14px; margin-top:4px;">
      This tool was built with <strong>Claude</strong> (Anthropic). If something breaks or you want to extend it,
      you can paste any of these PHP files into Claude and ask for help — it knows how the whole thing fits together.
    </p>
  </div>
</div>
</body>
</html>
