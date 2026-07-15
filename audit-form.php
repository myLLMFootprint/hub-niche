<?php
$NICHE_NAV_ACTIVE = 'new';
$state = trim($_GET['state'] ?? '');
$city = trim($_GET['city'] ?? '');
$population = trim($_GET['population'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Audit — <?= htmlspecialchars($city) ?>, <?= htmlspecialchars($state) ?> — hub.niche</title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5f6fa; margin: 0; color: #1e1e2e; }
  .wrap { max-width: 960px; margin: 0 auto; padding: 32px 20px; }
  h1 { font-size: 20px; margin-bottom: 4px; }
  .sub { color: #666; margin-bottom: 20px; font-size: 14px; }
  .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }
  label { display: block; font-size: 13px; font-weight: 600; margin: 14px 0 4px; }
  input[type=text], input[type=number] { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
  .row { display: flex; gap: 16px; }
  .row > div { flex: 1; }
  .btn { background: #4f46e5; color: #fff; border: none; padding: 11px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 18px; }
  .btn:hover:not(:disabled) { background: #4338ca; }
  .btn:disabled { opacity: .6; cursor: not-allowed; }
  .competitor-row { border: 1px dashed #ddd; border-radius: 8px; padding: 12px; margin-top: 10px; }
  a.change { font-size: 12px; color: #4f46e5; text-decoration: none; font-weight: 600; }
  .suggest-btn { font-size:12px; background:#eef2ff; color:#4338ca; border:none; padding:5px 12px; border-radius:5px; cursor:pointer; font-weight:600; margin-top:8px; }
  .chip { font-size:12px; background:#f5f5f7; border:1px solid #ddd; padding:5px 12px; border-radius:999px; cursor:pointer; color:#333; }
  .chip:hover { background:#e0e7ff; }

  /* Modal */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); align-items:center; justify-content:center; z-index:100; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:12px; padding:28px; max-width:520px; width:90%; max-height:85vh; overflow-y:auto; }
  .modal-close { float:right; background:none; border:none; font-size:20px; cursor:pointer; color:#999; }
  .score-big { font-size:44px; font-weight:800; }
  .pill { display: inline-block; padding: 5px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; }
  .pill-green { background: #dcfce7; color: #166534; }
  .pill-yellow { background: #fef9c3; color: #854d0e; }
  .pill-orange { background: #ffedd5; color: #9a3412; }
  .pill-red { background: #fee2e2; color: #991b1b; }
  ul.rec { padding-left: 18px; font-size: 13px; }
  ul.rec li { margin-bottom: 6px; }
  .modal-actions { margin-top: 20px; display:flex; gap:10px; }
  .btn-secondary { background:#eef2ff; color:#4338ca; }
  .btn-secondary:hover { background:#e0e7ff; }
  .inline-banner { background:#fff; border-radius:10px; padding:16px 20px; margin-bottom:16px; box-shadow: 0 1px 3px rgba(0,0,0,.08); display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }
  .inline-banner .score { font-size:24px; font-weight:800; }
</style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="wrap">
  <h1>Step 2 — Niche & Competitor Details</h1>
  <div class="sub">
    Location: <strong><?= htmlspecialchars($city) ?>, <?= htmlspecialchars($state) ?></strong>
    <?php if ($population): ?> (pop. <?= number_format((int)$population) ?>)<?php endif; ?>
    &nbsp;·&nbsp; <a class="change" href="research.php">Change location</a>
  </div>

  <div id="inlineResult" style="display:none;"></div>

  <div class="card">
    <form id="auditForm">
      <input type="hidden" name="state" value="<?= htmlspecialchars($state) ?>">
      <input type="hidden" name="city" value="<?= htmlspecialchars($city) ?>">

      <div class="row">
        <div>
          <label>City Population</label>
          <input type="number" name="population" value="<?= htmlspecialchars($population) ?>" required>
        </div>
        <div>
          <label>Niche</label>
          <input type="text" name="niche" id="nicheInput" placeholder="e.g. gutter installation" required>
          <button type="button" class="suggest-btn" id="suggestBtn">✨ Suggest keywords</button>
          <div id="keywordSuggestions" style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;"></div>
        </div>
      </div>

      <label style="margin-top:16px;">Target Keyword</label>
      <input type="text" name="keyword" id="keywordInput" placeholder="e.g. gutter installation <?= htmlspecialchars($city) ?> <?= htmlspecialchars($state) ?>">
      <button type="button" class="suggest-btn" id="useThisBtn">✅ Use This</button>
      <div id="searchTermBox" style="display:none; margin-top:10px; background:#f8f9ff; border:1px solid #e0e7ff; border-radius:8px; padding:14px;">
        <div style="font-size:12px; color:#888; font-weight:600; text-transform:uppercase; margin-bottom:6px;">Google search term</div>
        <code id="searchTermText" style="display:block; font-size:14px; color:#1e1b4b; background:#fff; border:1px solid #ddd; border-radius:6px; padding:8px 10px; margin-bottom:10px; word-break:break-word;"></code>
        <a id="googleSearchLink" href="#" target="_blank" rel="noopener" class="btn" style="text-decoration:none; display:inline-block; margin-top:0;">🔍 Open in Google →</a>
        <button type="button" class="suggest-btn" id="copyTermBtn" style="margin-left:6px;">📋 Copy</button>
        <div style="font-size:12px; color:#777; margin-top:10px;">
          Search this, then copy the top 3 ranking domains into the competitor fields below.
        </div>
      </div>

      <h3 style="margin-top:24px; font-size:15px; margin-bottom:4px;">Top 3 Competitor Sites</h3>
      <div style="font-size:12px; color:#777; margin-bottom:10px;">
        Paste each domain into the
        <a href="https://ahrefs.com/website-authority-checker" target="_blank" rel="noopener" style="color:#4f46e5; font-weight:600;">free Ahrefs checker ↗</a>
        (no signup) for Domain Rating + backlinks. Page count: Google <code>site:domain.com</code> and read the pagination.
        <strong>Leave a field blank if you didn't measure it</strong> — blanks score nothing rather than counting as weakness.
      </div>
      <?php for ($i = 1; $i <= 3; $i++): ?>
      <div class="competitor-row">
        <strong>Competitor #<?= $i ?></strong>
        <div class="row" style="margin-top:8px;">
          <div><label>Domain</label><input type="text" name="comp<?= $i ?>_domain" placeholder="example.com"></div>
          <div><label>Domain Rating / DA <span style="font-weight:400;color:#999;">(Ahrefs)</span></label><input type="number" name="comp<?= $i ?>_da" min="0" max="100"></div>
          <div><label>Backlink Count <span style="font-weight:400;color:#999;">(Ahrefs)</span></label><input type="number" name="comp<?= $i ?>_backlinks" min="0"></div>
        </div>
        <div class="row" style="margin-top:8px;">
          <div><label>Page Count <span style="font-weight:400;color:#999;">(site: search)</span></label><input type="number" name="comp<?= $i ?>_pages" min="0"></div>
          <div><label>Site Last Updated (year) <span style="font-weight:400;color:#999;">(footer)</span></label><input type="number" name="comp<?= $i ?>_year" placeholder="e.g. 2019"></div>
        </div>
        <label style="display:inline-block; margin-right:16px;"><input type="checkbox" name="comp<?= $i ?>_images" value="1"> Has good images</label>
        <label style="display:inline-block;"><input type="checkbox" name="comp<?= $i ?>_headings" value="1"> Headings well-structured</label>
      </div>
      <?php endfor; ?>

      <button type="submit" class="btn" id="saveBtn">Save & Score Audit</button>
    </form>
  </div>
</div>

<!-- Results modal — the full report is optional, shown here inline instead of forcing a page redirect -->
<div class="modal-overlay" id="resultModal">
  <div class="modal-box">
    <button class="modal-close" id="modalClose">&times;</button>
    <div id="modalContent">Loading...</div>
  </div>
</div>

<script>
// "Use This" — locks in the current keyword and hands back a ready-to-click
// Google search. No API, no cost: you run the search yourself and read the
// top 3 organic results straight off the page.
document.getElementById('useThisBtn').addEventListener('click', function() {
  const keyword = document.getElementById('keywordInput').value.trim();
  const box = document.getElementById('searchTermBox');
  const termText = document.getElementById('searchTermText');
  const link = document.getElementById('googleSearchLink');

  if (!keyword) {
    alert('Enter or pick a target keyword first.');
    return;
  }

  termText.textContent = keyword;
  link.href = 'https://www.google.com/search?q=' + encodeURIComponent(keyword);
  box.style.display = 'block';
  box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
});

document.getElementById('copyTermBtn').addEventListener('click', function() {
  const keyword = document.getElementById('searchTermText').textContent;
  navigator.clipboard.writeText(keyword).then(() => {
    const original = this.textContent;
    this.textContent = '✅ Copied';
    setTimeout(() => { this.textContent = original; }, 1500);
  }).catch(() => {
    alert('Copy failed — select the text above and copy manually.');
  });
});

const CITY = <?= json_encode($city) ?>;
const STATE = <?= json_encode($state) ?>;

function renderKeywordSuggestions() {
  const niche = document.getElementById('nicheInput').value.trim();
  const box = document.getElementById('keywordSuggestions');
  const keywordField = document.getElementById('keywordInput');

  if (!niche) {
    box.innerHTML = '';
    return;
  }

  // Keyword variants — "towing Asheville", "best towing Asheville", and
  // "affordable towing near me" all rank differently,
  // which is part of why the model isn't saturated.
  const variants = [
    `${niche} ${CITY}`,
    `${niche} ${CITY} ${STATE}`,
    `best ${niche} ${CITY}`,
    `affordable ${niche} ${CITY}`,
    `${niche} near me`,
    `${niche} ${CITY} reviews`,
    `top ${niche} in ${CITY}`,
    `emergency ${niche} ${CITY}`,
    `cheap ${niche} ${CITY}`,
    `local ${niche} ${CITY}`,
  ];

  // Default the keyword field to the primary variant, unless the user has
  // already typed/picked something themselves.
  if (!keywordField.value.trim() || keywordField.dataset.autofilled === 'true') {
    keywordField.value = variants[0];
    keywordField.dataset.autofilled = 'true';
  }

  box.innerHTML = '';
  variants.forEach(v => {
    const chip = document.createElement('button');
    chip.type = 'button';
    chip.className = 'chip';
    chip.textContent = v;
    chip.addEventListener('click', () => {
      keywordField.value = v;
      keywordField.dataset.autofilled = 'false'; // user made an explicit choice
    });
    box.appendChild(chip);
  });
}

// Show suggestions live as the niche is typed — no button click needed
document.getElementById('nicheInput').addEventListener('input', renderKeywordSuggestions);

// If the user edits the keyword field by hand, stop auto-overwriting it
document.getElementById('keywordInput').addEventListener('input', function() {
  this.dataset.autofilled = 'false';
});

// Button kept as a manual refresh/re-roll
document.getElementById('suggestBtn').addEventListener('click', renderKeywordSuggestions);

// ── Submit via AJAX, show results in a modal instead of redirecting ──
const form = document.getElementById('auditForm');
const modal = document.getElementById('resultModal');
const modalContent = document.getElementById('modalContent');
const saveBtn = document.getElementById('saveBtn');

document.getElementById('modalClose').addEventListener('click', () => modal.classList.remove('open'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('open'); });

form.addEventListener('submit', async function(e) {
  e.preventDefault();
  saveBtn.disabled = true;
  saveBtn.textContent = 'Scoring...';

  try {
    const formData = new FormData(form);
    const resp = await fetch('audit-save.php', { method: 'POST', body: formData });
    const rawText = await resp.text();
    console.log('audit-save raw response:', rawText);

    let data;
    try {
      data = JSON.parse(rawText);
    } catch (parseErr) {
      saveBtn.disabled = false;
      saveBtn.textContent = 'Save & Score Audit';
      modalContent.innerHTML = '<p style="color:#c0392b;">Server returned an invalid response — check the console for the raw output.</p>';
      modal.classList.add('open');
      return;
    }

    saveBtn.disabled = false;
    saveBtn.textContent = 'Save & Score Audit';

    if (data.error) {
      modalContent.innerHTML = `<p style="color:#c0392b;">${data.error}</p>`;
      modal.classList.add('open');
      return;
    }

    const r = data.result;
    let pillClass = 'pill-red';
    if (r.rating.label.includes('Strong')) pillClass = 'pill-green';
    else if (r.rating.label.includes('Workable')) pillClass = 'pill-yellow';
    else if (r.rating.label.includes('Marginal')) pillClass = 'pill-orange';

    // Build the modal content (shown only if the user clicks "View Full Report")
    modalContent.innerHTML = `
      <h2 style="margin-top:0;">${data.niche} — ${data.city}, ${data.state}</h2>
      <div class="score-big">${r.total}%</div>
      <span class="pill ${pillClass}">${r.rating.emoji} ${r.rating.label}</span>
      <h3 style="margin-top:20px; font-size:14px;">Recommendations</h3>
      <ul class="rec">${data.recommendations.map(rec => `<li>${rec}</li>`).join('')}</ul>
      <div class="modal-actions">
        <a class="btn" style="text-decoration:none;" href="view-audit.php?id=${encodeURIComponent(data.id)}&slug=${encodeURIComponent(data.slug)}">Open Full Report Page →</a>
        <a class="btn btn-secondary" style="text-decoration:none;" href="research.php">+ New Audit</a>
      </div>
    `;

    // Inline banner — saved result stays visible without covering the form
    const inlineResult = document.getElementById('inlineResult');
    inlineResult.style.display = 'block';
    inlineResult.innerHTML = `
      <div class="inline-banner">
        <div>
          <span class="score">${r.total}%</span>
          &nbsp; <span class="pill ${pillClass}">${r.rating.emoji} ${r.rating.label}</span>
          &nbsp; <span style="color:#666; font-size:13px;">Saved — ${data.niche}, ${data.city}</span>
        </div>
        <div>
          <button type="button" class="suggest-btn" id="viewResultsBtn">View Full Results</button>
          <a class="suggest-btn" style="text-decoration:none; display:inline-block;" href="view-audit.php?id=${encodeURIComponent(data.id)}&slug=${encodeURIComponent(data.slug)}">Open Report Page →</a>
        </div>
      </div>
    `;
    document.getElementById('viewResultsBtn').addEventListener('click', () => modal.classList.add('open'));
    inlineResult.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } catch (err) {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Save & Score Audit';
    modalContent.innerHTML = '<p style="color:#c0392b;">Something went wrong saving the audit — check the console.</p>';
    modal.classList.add('open');
    console.error(err);
  }
});
</script>
</body>
</html>
