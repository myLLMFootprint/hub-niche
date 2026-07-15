<?php
/**
 * hub.niche — Audit Save Handler
 * Computes the niche/city/competition score and writes it to
 * /hub.niche/projects/{slug}/audit.json (mirrors the new-panel-11 per-project pattern).
 */

session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/niche-criteria.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES); }

$niche      = trim($_POST['niche'] ?? '');
$state      = trim($_POST['state'] ?? '');
$city       = trim($_POST['city'] ?? '');
$population = (int)($_POST['population'] ?? 0);
$keyword    = trim($_POST['keyword'] ?? '');

if ($niche === '' || $city === '' || $state === '' || $population <= 0) {
    echo json_encode(['error' => 'Missing required fields — niche, city, state, and population are all required.']);
    exit;
}

// ── Build competitor array from POST ──
$competitors = [];
for ($i = 1; $i <= 3; $i++) {
    $domain = trim($_POST["comp{$i}_domain"] ?? '');
    if ($domain === '') continue; // skip empty rows
    // A blank field must stay null. Casting it to (int) turns "" into 0, which
    // the scorer would read as "domain authority zero — excellent" and hand out
    // points that were never earned.
    $numOrNull = function ($key) {
        $v = $_POST[$key] ?? '';
        return ($v === '' || $v === null) ? null : (int)$v;
    };

    $competitors[] = [
        'domain' => $domain,
        'domain_authority' => $numOrNull("comp{$i}_da"),
        'backlink_count' => $numOrNull("comp{$i}_backlinks"),
        'page_count' => $numOrNull("comp{$i}_pages"),
        'has_images' => !empty($_POST["comp{$i}_images"]),
        'headings_structured' => !empty($_POST["comp{$i}_headings"]),
        'last_updated_year' => $numOrNull("comp{$i}_year"),
    ];
}

// ── Score everything using the shared library ──
$nicheScore = scoreNicheFit($niche);
$cityScore  = scoreCityPopulation($population);

$competitorScores = [];
foreach ($competitors as $c) {
    $s = scoreCompetitorSite($c);
    $s['domain'] = $c['domain'];
    $competitorScores[] = $s;
}

$overall = computeOverallScore($nicheScore, $cityScore, $competitorScores);

// ── Build the recommendation list (plain-language, matches the report style used elsewhere) ──
$recommendations = [];
if ($nicheScore['score'] <= 2) {
    $recommendations[] = "This niche is on the avoid list — {$nicheScore['note']} Consider a niche-of-a-niche angle instead (e.g. a specific sub-service) to sidestep the competition.";
}
if ($cityScore['score'] <= 6) {
    $recommendations[] = "City population is outside the sweet spot ({$cityScore['label']}) — {$cityScore['note']}";
}
foreach ($competitorScores as $c) {
    if ($c['score'] <= 4) {
        $recommendations[] = "Competitor {$c['domain']} looks strong (score {$c['score']}/12) — this keyword may take longer than the typical 2–6 month window.";
    }
}
if (empty($recommendations)) {
    $recommendations[] = "This looks like a solid opportunity across the board — proceed with the content + backlink plan (hyper-local pages, copycat backlink method).";
}

// ── Save ──
$id = 'niche_' . time() . '_' . substr(md5(uniqid()), 0, 6);
$slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($niche . '-' . $city . '-' . $state . '-' . time()));
$projectDir = HUBNICHE_DATA_DIR . '/' . $slug;
if (!is_dir($projectDir)) { mkdir($projectDir, 0755, true); }

$record = [
    'id' => $id,
    'niche' => $niche,
    'state' => $state,
    'city' => $city,
    'population' => $population,
    'keyword' => $keyword,
    'niche_score' => $nicheScore,
    'city_score' => $cityScore,
    'competitor_scores' => $competitorScores,
    'result' => $overall,
    'recommendations' => $recommendations,
    'created_at' => date('c'),
];

file_put_contents($projectDir . '/audit.json', json_encode($record, JSON_PRETTY_PRINT));

echo json_encode(array_merge($record, ['slug' => $slug]));
exit;
