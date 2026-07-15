<?php
/**
 * hub.niche — Niche & City Opportunity Scoring Library
 *
 * Every rule in this file is sourced directly from the transcript:
 * a general framework for evaluating local service lead-generation ("rank and rent") opportunities
 *
 * This is a PURE logic file — no output, no session handling. Include it
 * from index.php / audit-save.php / view-audit.php.
 */

// ─────────────────────────────────────────────────────────────
// NICHE LISTS — services that fit the local lead-gen model well vs. poorly
// ─────────────────────────────────────────────────────────────

// Each entry has 'aliases' (substrings to match against user input) and a 'note'.
// Aliases exist because people type "gutter installer" / "gutters" / "tree trimming"
// rather than the exact phrase used in the transcript.

$NICHE_TARGET_LIST = [
    'tree service' => [
        'aliases' => ['tree service', 'tree removal', 'tree trimming', 'tree trimmer', 'arborist', 'stump grinding', 'stump removal'],
        'note' => 'Named as his own example niche; low competition in most cities.',
    ],
    'gutter installation' => [
        'aliases' => ['gutter', 'gutters', 'gutter guard', 'leaf guard', 'downspout'],
        'note' => 'Named directly as a target niche.',
    ],
    'epoxy floors' => [
        'aliases' => ['epoxy', 'epoxy floor', 'garage floor coating', 'concrete coating'],
        'note' => 'Named directly as a target niche.',
    ],
    'popcorn ceiling removal' => [
        'aliases' => ['popcorn ceiling', 'ceiling removal', 'ceiling texture'],
        'note' => 'Named as a "niche of a niche" — his friend landed a $20,000 commercial quote through one site.',
    ],
    'junk removal' => [
        'aliases' => ['junk removal', 'junk hauling', 'hauling', 'debris removal', 'dumpster rental'],
        'note' => 'Named directly as a target niche.',
    ],
    'septic' => [
        'aliases' => ['septic', 'septic tank', 'septic pumping', 'grease trap'],
        'note' => 'Unglamorous, urgent, high-ticket, and often served by the least SEO-savvy incumbents.',
    ],
    'towing' => [
        'aliases' => ['towing', 'tow truck', 'roadside assistance', 'wrecker'],
        'note' => 'Used in his keyword-variant example (towing / best towing / affordable towing).',
    ],
    'auto glass repair' => [
        'aliases' => ['auto glass', 'autoglass', 'windshield', 'windshield repair', 'windshield replacement'],
        'note' => 'His flagship example — the New Orleans autoglass site he pinned above the map pack.',
    ],
    'water treatment' => [
        'aliases' => ['water treatment', 'reverse osmosis', 'water softener', 'water filtration', 'well water'],
        'note' => 'High-ticket install work, often region-specific demand, uncrowded in many markets.',
    ],
    'pressure washing' => [
        'aliases' => ['pressure washing', 'power washing', 'soft wash', 'driveway cleaning'],
        'note' => 'Home service, phone-driven, with low incumbent SEO sophistication in many markets.',
    ],
    'fencing' => [
        'aliases' => ['fence', 'fencing', 'fence installation', 'fence repair'],
        'note' => 'Home service, phone-driven, with low incumbent SEO sophistication in many markets.',
    ],
];

$NICHE_AVOID_LIST = [
    'plumbing' => [
        'aliases' => ['plumbing', 'plumber', 'drain cleaning', 'water heater'],
        'note' => 'Named as one of the niches private equity has eaten away — avoid in most metros.',
    ],
    'hvac' => [
        'aliases' => ['hvac', 'heating and cooling', 'air conditioning', 'ac repair', 'furnace'],
        'note' => 'Named as PE-saturated.',
    ],
    'roofing' => [
        'aliases' => ['roofing', 'roofer', 'roof repair', 'roof replacement'],
        'note' => 'Competitive and often agency-managed — workable in some small markets, but difficult.',
    ],
    'personal injury law' => [
        'aliases' => ['personal injury', 'injury lawyer', 'injury attorney', 'accident lawyer', 'law firm', 'attorney'],
        'note' => 'His hardest example: LA personal injury lawyers spending around $50,000/month on marketing. Do not compete here.',
    ],
];

// ─────────────────────────────────────────────────────────────
// CITY SIZE RULES
// ─────────────────────────────────────────────────────────────
// Over 800 US cities have 50k+ population. Below ~100k population,
// most operators are "a guy with a pickup truck and a tool belt" who can't
// justify $1,500–10k/month agency retainers. Bigger cities = more search
// volume/demand but tougher competition (explicit tradeoff he gives Ryan).

$CITY_SWEET_SPOT_MIN = 50000;
$CITY_SWEET_SPOT_MAX = 100000;   // "easy mode" — weak/no real competition
$CITY_STRETCH_MAX    = 250000;   // still workable, more demand, more competition

function scoreCityPopulation(int $population): array {
    global $CITY_SWEET_SPOT_MIN, $CITY_SWEET_SPOT_MAX, $CITY_STRETCH_MAX;

    if ($population < $CITY_SWEET_SPOT_MIN) {
        return ['score' => 6, 'label' => 'Below sweet spot', 'note' => 'May lack enough call volume to reliably sustain a rental.'];
    }
    if ($population <= $CITY_SWEET_SPOT_MAX) {
        return ['score' => 10, 'label' => 'Sweet spot', 'note' => 'The ideal size — likely small operators with no SEO investment.'];
    }
    if ($population <= $CITY_STRETCH_MAX) {
        return ['score' => 7, 'label' => 'Workable, more competitive', 'note' => 'More search demand, but expect stronger competing sites.'];
    }
    return ['score' => 4, 'label' => 'Large metro', 'note' => 'Higher demand but likely well-funded or agency-managed competitors.'];
}

// ─────────────────────────────────────────────────────────────
// NICHE FIT SCORING
// ─────────────────────────────────────────────────────────────

function scoreNicheFit(string $niche): array {
    global $NICHE_TARGET_LIST, $NICHE_AVOID_LIST;
    $niche_lower = strtolower(trim($niche));

    // Avoid list wins ties — a niche on the avoid list is disqualifying
    // regardless of what else it might partially match.
    foreach ($NICHE_AVOID_LIST as $canonical => $entry) {
        foreach ($entry['aliases'] as $alias) {
            if (str_contains($niche_lower, $alias)) {
                return [
                    'score' => 2,
                    'label' => 'On avoid list',
                    'matched' => $canonical,
                    'note' => $entry['note'],
                ];
            }
        }
    }

    foreach ($NICHE_TARGET_LIST as $canonical => $entry) {
        foreach ($entry['aliases'] as $alias) {
            if (str_contains($niche_lower, $alias)) {
                return [
                    'score' => 10,
                    'label' => 'On target list',
                    'matched' => $canonical,
                    'note' => $entry['note'],
                ];
            }
        }
    }

    return [
        'score' => 6,
        'label' => 'Unlisted — needs manual check',
        'matched' => null,
        'note' => 'Not a pre-listed niche. Judge it directly: is it a phone-driven home service where incumbents have not invested in SEO?',
    ];
}

// ─────────────────────────────────────────────────────────────
// COMPETITION SCORING RUBRIC
// ─────────────────────────────────────────────────────────────
// A manual competitor-assessment process, translated into a scorable rubric:
// "You're looking at site number one... their backlinks, their trust flow,
// their domain authority, how much content do they have, does it have a
// good amount of pictures, are the headings properly structured."
// Weak competition = "looks like it was built in 2005 and hasn't been touched since."

function scoreCompetitorSite(array $site): array {
    // A field left blank scores NOTHING and does not count toward the max.
    // Only what you actually measured moves the number. This matters because
    // an unmeasured field is not evidence of weakness — the earlier version
    // treated blanks as wins and quietly inflated every score.
    $score = 0;
    $max = 0;
    $notes = [];
    $unknown = [];

    // Domain authority / domain rating (weight 3 — the strongest single signal)
    $da = $site['domain_authority'];
    if ($da === null || $da === '') {
        $unknown[] = 'domain authority';
    } else {
        $max += 3;
        $da = (int)$da;
        if ($da < 10)      { $score += 3; $notes[] = "Domain authority {$da} — essentially no link authority."; }
        elseif ($da < 25)  { $score += 2; $notes[] = "Domain authority {$da} — weak, beatable."; }
        elseif ($da < 40)  { $score += 1; $notes[] = "Domain authority {$da} — moderate."; }
        else               { $notes[] = "Domain authority {$da} — strong, hard to displace."; }
    }

    // Backlink count (weight 2)
    $bl = $site['backlink_count'];
    if ($bl === null || $bl === '') {
        $unknown[] = 'backlinks';
    } else {
        $max += 2;
        $bl = (int)$bl;
        if ($bl < 50)       { $score += 2; $notes[] = "{$bl} backlinks — thin profile, beatable via the copycat method."; }
        elseif ($bl < 250)  { $score += 1; $notes[] = "{$bl} backlinks — modest profile."; }
        else                { $notes[] = "{$bl} backlinks — established profile."; }
    }

    // Page count (weight 3 — content depth is what you have to out-build)
    $pc = $site['page_count'];
    if ($pc === null || $pc === '') {
        $unknown[] = 'page count';
    } else {
        $max += 3;
        $pc = (int)$pc;
        if ($pc < 5)       { $score += 3; $notes[] = "Only {$pc} pages — brochure site, easy to out-build."; }
        elseif ($pc < 15)  { $score += 2; $notes[] = "{$pc} pages — light content footprint."; }
        elseif ($pc < 40)  { $score += 1; $notes[] = "{$pc} pages — real content investment."; }
        else               { $notes[] = "{$pc} pages — heavily built out; matching this is significant work."; }
    }

    // Images and headings (weight 1 each) — these are checkboxes, so an
    // unchecked box genuinely means "not good", not "unknown".
    $max += 1;
    if (empty($site['has_images'])) { $score += 1; $notes[] = 'Weak imagery — easy visual upgrade.'; }

    $max += 1;
    if (empty($site['headings_structured'])) { $score += 1; $notes[] = 'Poor heading structure — easy on-page fix.'; }

    // Last updated year (weight 2)
    $yr = $site['last_updated_year'];
    if ($yr === null || $yr === '' || (int)$yr < 1990) {
        $unknown[] = 'last updated';
    } else {
        $max += 2;
        $yr = (int)$yr;
        $age = (int)date('Y') - $yr;
        if ($age >= 5)     { $score += 2; $notes[] = "Last updated {$yr} — stale, the 'built years ago and never touched' tell."; }
        elseif ($age >= 2) { $score += 1; $notes[] = "Last updated {$yr} — somewhat neglected."; }
        else               { $notes[] = "Last updated {$yr} — actively maintained."; }
    }

    if (!empty($unknown)) {
        $notes[] = 'Not measured: ' . implode(', ', $unknown) . ' (scored nothing either way).';
    }

    return [
        'score' => $score,
        'max' => $max,
        'notes' => $notes,
        'unknown_count' => count($unknown),
    ];
}

// ─────────────────────────────────────────────────────────────
// OVERALL AUDIT SCORE
// ─────────────────────────────────────────────────────────────
// Weighted by how much each factor tends to determine the outcome.

function computeOverallScore(array $nicheScore, array $cityScore, array $top3CompetitorScores): array {
    // Each competitor now carries its own max (fields left blank don't count),
    // so average the RATIO rather than the raw points.
    $competitionRatio = null;
    $ratios = [];
    foreach ($top3CompetitorScores as $c) {
        if (($c['max'] ?? 0) > 0) {
            $ratios[] = $c['score'] / $c['max'];
        }
    }
    if (!empty($ratios)) {
        $competitionRatio = array_sum($ratios) / count($ratios);
    }

    // Weights: niche fit 30%, city fit 20%, competition weakness 50%.
    // Competition carries the most weight — it's the single biggest
    // determinant of whether you can realistically rank.
    $nichePct = ($nicheScore['score'] / 10) * 30;
    $cityPct  = ($cityScore['score'] / 10) * 20;

    if ($competitionRatio === null) {
        // No competitor data at all. Don't invent a score — re-weight niche and
        // city to fill the space and flag that the number is provisional.
        $nichePct = ($nicheScore['score'] / 10) * 60;
        $cityPct  = ($cityScore['score'] / 10) * 40;
        $compPct  = 0;
        $total = round($nichePct + $cityPct);

        return [
            'total' => $total,
            'niche_pct' => round($nichePct, 1),
            'city_pct' => round($cityPct, 1),
            'competition_pct' => null,
            'competition_measured' => false,
            'rating' => ['emoji' => '⚪', 'label' => 'Provisional — no competitor data entered yet'],
        ];
    }

    $compPct = $competitionRatio * 50;
    $total = round($nichePct + $cityPct + $compPct);

    if ($total >= 80) $rating = ['emoji' => '🟢', 'label' => 'Strong opportunity — build it'];
    elseif ($total >= 60) $rating = ['emoji' => '🟡', 'label' => 'Workable — proceed with a solid content plan'];
    elseif ($total >= 40) $rating = ['emoji' => '🟠', 'label' => 'Marginal — consider a different city or niche-of-a-niche angle'];
    else $rating = ['emoji' => '🔴', 'label' => 'Skip — competition or niche too strong'];

    return [
        'total' => $total,
        'niche_pct' => round($nichePct, 1),
        'city_pct' => round($cityPct, 1),
        'competition_pct' => round($compPct, 1),
        'competition_measured' => true,
        'rating' => $rating,
    ];
}
