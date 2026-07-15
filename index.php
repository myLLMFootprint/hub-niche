<?php
$NICHE_NAV_ACTIVE = 'read';
require_once __DIR__ . '/config.php';

// ── EDIT THIS: paste your GitHub repo URL here once created ──
$GITHUB_URL = 'https://github.com/myLLMFootprint/hub-niche';

// Live environment check
$phpOk = version_compare(PHP_VERSION, '7.4', '>=');
$curlOk = function_exists('curl_init');
$dataWritable = is_dir(HUBNICHE_DATA_DIR) && is_writable(HUBNICHE_DATA_DIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>hub.niche — Read First</title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5f6fa; margin: 0; color: #1e1e2e; line-height: 1.6; }
  .wrap { max-width: 820px; margin: 0 auto; padding: 36px 20px 80px; }
  .hero { text-align: center; padding: 20px 0 8px; }
  .hero .kicker { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #c8871c; font-weight: 700; }
  h1 { font-size: 34px; letter-spacing: -1px; margin: 8px 0 6px; }
  h1 span { color: #e8a838; }
  .hero p { color: #666; font-size: 16px; max-width: 560px; margin: 0 auto 22px; }
  .cta-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 8px; }
  .btn { display: inline-flex; align-items: center; gap: 8px; background: #14213d; color: #fff; border: none; padding: 13px 24px; border-radius: 9px; font-size: 15px; font-weight: 600; text-decoration: none; cursor: pointer; }
  .btn:hover { background: #1f2f52; }
  .btn-amber { background: #e8a838; color: #14213d; }
  .btn-amber:hover { background: #c8871c; }
  .btn-ghost { background: #eef0f4; color: #14213d; }
  .btn-ghost:hover { background: #e2e5ec; }
  .card { background: #fff; border-radius: 12px; padding: 26px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 22px; }
  .eyebrow { font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: #c8871c; font-weight: 700; margin-bottom: 10px; }
  h2 { font-size: 19px; margin: 0 0 12px; }
  p { margin: 0 0 12px; }
  ul { margin: 0 0 8px; padding-left: 20px; }
  li { margin-bottom: 8px; }
  code { background: #f0efea; padding: 2px 6px; border-radius: 4px; font-size: 13px; font-family: "SF Mono", Menlo, monospace; }
  .req { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #eee; font-size: 14px; }
  .req:last-child { border-bottom: none; }
  .dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .dot.ok { background: #2f7d54; } .dot.no { background: #b4402f; }
  .badge { font-size: 11px; padding: 2px 9px; border-radius: 12px; font-weight: 600; }
  .badge.ok { background: #e2f0e7; color: #166534; } .badge.no { background: #f6ddd7; color: #b4402f; }
  .steps { counter-reset: s; }
  .steps .s { display: flex; gap: 14px; margin-bottom: 16px; }
  .steps .s .n { counter-increment: s; flex-shrink: 0; width: 28px; height: 28px; border-radius: 50%; background: #14213d; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
  .steps .s .n::before { content: counter(s); }
  .steps .s .b { font-size: 14px; }
  .gh { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; background: #14213d; color: #fff; border-radius: 12px; padding: 22px 26px; margin-bottom: 22px; }
  .gh .t { font-size: 16px; font-weight: 700; }
  .gh .d { font-size: 13px; color: #b7bdcc; }
  .gh code { background: rgba(255,255,255,.12); color: #e6e9f0; }
  .callout { background: #faf7ee; border-left: 3px solid #e8a838; padding: 12px 16px; border-radius: 6px; font-size: 14px; margin: 14px 0; }
  .support { font-size: 13px; color: #888; }
  a { color: #c8871c; font-weight: 600; }
</style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="wrap">

  <div class="hero">
    <div class="kicker">Read First</div>
    <h1>hub<span>.niche</span></h1>
    <p>A self-hosted tool for scoring whether a niche and city are worth building a local lead-generation site for. Pick a market, size up who's ranking, get a clear opportunity score.</p>
    <div class="cta-row">
      <a href="research.php" class="btn btn-amber">Start an audit →</a>
      <a href="<?= htmlspecialchars($GITHUB_URL) ?>" target="_blank" rel="noopener" class="btn btn-ghost">View on GitHub ↗</a>
    </div>
  </div>

  <!-- GITHUB -->
  <div class="gh">
    <div>
      <div class="t">All the files live on GitHub</div>
      <div class="d">Clone or download the full tool, get updates, and report issues there.</div>
    </div>
    <a href="<?= htmlspecialchars($GITHUB_URL) ?>" target="_blank" rel="noopener" class="btn btn-amber">Open repository ↗</a>
  </div>

  <!-- WHAT IT DOES -->
  <div class="card">
    <div class="eyebrow">What it does</div>
    <h2>Score a market before you build</h2>
    <p>You pick a state and city, name a local service (gutters, septic, tree work, and so on), then check the top three sites currently ranking for your keyword. hub.niche weights it into a single 0–100 opportunity score with a plain-language verdict, and saves every audit to your own server so your research builds up over time.</p>
    <p style="font-size:14px; color:#666;">The score weights service fit (30%), city size (20%), and how beatable the ranking competitors are (50%). It favors phone-driven home services in mid-size markets where incumbents haven't invested in SEO. It's a decision aid — always sanity-check search demand before committing.</p>
  </div>

  <!-- REQUIREMENTS + LIVE CHECK -->
  <div class="card">
    <div class="eyebrow">Requirements</div>
    <h2>What you need</h2>
    <ul>
      <li>Web host running <strong>PHP 7.4+</strong> (standard cPanel/Apache shared hosting is fine)</li>
      <li><strong>cURL</strong> enabled (on by default almost everywhere)</li>
      <li>A way to upload files — cPanel File Manager, FTP, or SSH</li>
      <li><strong>No database</strong> and <strong>no API keys</strong> required</li>
    </ul>
    <div class="callout">Optional: a paid <strong>Ahrefs API</strong> key if you want competitor metrics to auto-fill. Everything works without it — see <a href="settings.php">Settings</a>.</div>

    <div style="margin-top:18px;">
      <div class="eyebrow">This server — live check</div>
      <div class="req"><span class="dot <?= $phpOk?'ok':'no' ?>"></span><span style="flex:1;">PHP 7.4+ <span style="color:#999;">(you have <?= PHP_VERSION ?>)</span></span><span class="badge <?= $phpOk?'ok':'no' ?>"><?= $phpOk?'OK':'Upgrade' ?></span></div>
      <div class="req"><span class="dot <?= $curlOk?'ok':'no' ?>"></span><span style="flex:1;">cURL extension</span><span class="badge <?= $curlOk?'ok':'no' ?>"><?= $curlOk?'OK':'Missing' ?></span></div>
      <div class="req"><span class="dot <?= $dataWritable?'ok':'no' ?>"></span><span style="flex:1;"><code>projects/</code> folder writable</span><span class="badge <?= $dataWritable?'ok':'no' ?>"><?= $dataWritable?'OK':'Fix perms' ?></span></div>
    </div>
    <?php if (!$dataWritable): ?>
    <div class="callout" style="margin-top:14px;">The <code>projects/</code> folder isn't writable yet. Create it inside <code>hub.niche/</code> and set permissions to <code>755</code> (or <code>775</code> on stricter hosts).</div>
    <?php endif; ?>
  </div>

  <!-- QUICK INSTALL -->
  <div class="card">
    <div class="eyebrow">Install in 4 steps</div>
    <h2>Getting it running</h2>
    <div class="steps">
      <div class="s"><div class="n"></div><div class="b">Download the files from <a href="<?= htmlspecialchars($GITHUB_URL) ?>" target="_blank" rel="noopener">GitHub</a> (clone or "Download ZIP").</div></div>
      <div class="s"><div class="n"></div><div class="b">Upload them into a <code>hub.niche/</code> folder in your web root, e.g. <code>/public_html/hub.niche/</code>.</div></div>
      <div class="s"><div class="n"></div><div class="b">Create a <code>projects/</code> folder inside it and set permissions to <code>755</code> (where audits save).</div></div>
      <div class="s"><div class="n"></div><div class="b">Visit <code>yourdomain.com/hub.niche/</code> — this page's live check confirms it's wired right. Then hit <a href="research.php">Start an audit</a>.</div></div>
    </div>
    <p style="font-size:13px; color:#888; margin-top:6px;">Full instructions and the optional Ahrefs setup are in the GitHub README.</p>
  </div>

  <!-- HOW TO USE -->
  <div class="card">
    <div class="eyebrow">Using it</div>
    <h2>Running an audit</h2>
    <ul>
      <li><strong>Pick a market</strong> — state, then main city (or a smaller nearby city, often weaker competition)</li>
      <li><strong>Name the service</strong> — keyword suggestions appear as you type</li>
      <li><strong>See who's ranking</strong> — one click opens Google for your keyword</li>
      <li><strong>Measure each competitor</strong> — free <a href="https://ahrefs.com/website-authority-checker" target="_blank" rel="noopener">Ahrefs checker</a> for Domain Rating + backlinks; <code>site:domain.com</code> for page count</li>
      <li><strong>Save &amp; score</strong> — get a verdict, saved to your History</li>
    </ul>
    <div style="margin-top:6px;"><a href="research.php" class="btn btn-amber">Start an audit →</a></div>
  </div>

  <!-- SUPPORT -->
  <div class="card">
    <div class="eyebrow">Support</div>
    <p class="support">Built with <strong>Claude</strong> (Anthropic). To get help or extend the tool, paste any of the PHP files into Claude and describe what you need — it understands how the pieces fit together. Bug reports and requests can also go in the <a href="<?= htmlspecialchars($GITHUB_URL) ?>/issues" target="_blank" rel="noopener">GitHub issues</a>.</p>
  </div>

</div>
</body>
</html>
