<?php
/**
 * hub.niche — Shared Nav Partial
 * Include at the top of every page's <body>.
 */
?>
<style>
  .niche-nav { background:#14213d; padding: 14px 24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
  .niche-nav .brand { color:#fff; font-weight:800; font-size:16px; letter-spacing:-0.3px; }
  .niche-nav .brand span { color:#e8a838; }
  .niche-nav .links a { color:#c2c8d6; text-decoration:none; font-size:13px; font-weight:600; margin-left:16px; }
  .niche-nav .links a:hover { color:#fff; }
  .niche-nav .links a.active { color:#14213d; background:#e8a838; padding:5px 11px; border-radius:6px; }
</style>
<div class="niche-nav">
  <div class="brand">📊 hub<span>.niche</span></div>
  <div class="links">
    <a href="index.php" class="<?= ($NICHE_NAV_ACTIVE ?? '') === 'read' ? 'active' : '' ?>">Read First</a>
    <a href="research.php" class="<?= ($NICHE_NAV_ACTIVE ?? '') === 'new' ? 'active' : '' ?>">+ New Audit</a>
    <a href="history.php" class="<?= ($NICHE_NAV_ACTIVE ?? '') === 'history' ? 'active' : '' ?>">History</a>
    <a href="settings.php" class="<?= ($NICHE_NAV_ACTIVE ?? '') === 'settings' ? 'active' : '' ?>">Settings</a>
  </div>
</div>
