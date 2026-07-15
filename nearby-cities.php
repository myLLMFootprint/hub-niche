<?php
/**
 * hub.niche — Nearby Small Cities Search
 * Free data sources (no API key required):
 *   1. Nominatim (OpenStreetMap) — geocodes "City, State" to lat/lon
 *   2. Overpass API (OpenStreetMap) — finds nearby place nodes with population tags
 *
 * NOTE: Nominatim's usage policy requires a descriptive User-Agent and caps
 * heavy use — fine for occasional manual audits, not for bulk automation.
 * If you outgrow this, swap in the US Census Gazetteer files or a paid
 * geocoding API instead.
 */

header('Content-Type: application/json');

$state = trim($_GET['state'] ?? '');
$city  = trim($_GET['city'] ?? '');
$radiusKm = (int)($_GET['radius'] ?? 40); // default 40km search radius

if ($state === '' || $city === '') {
    echo json_encode(['error' => 'Missing state or city.']);
    exit;
}

function httpGet(string $url, array $headers = []): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $result = curl_exec($ch);
    $err = curl_errno($ch);
    $errMsg = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['body' => $err ? null : $result, 'err' => $err, 'errMsg' => $errMsg, 'httpCode' => $httpCode];
}

// ── Step 1: Geocode the main city ──
$geocodeUrl = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
    'q' => "$city, $state, USA",
    'format' => 'json',
    'limit' => 1,
]);

$geo = httpGet($geocodeUrl, ['User-Agent: HubNicheAuditTool/1.0 (contact@agencyvideopartner.com)']);
$geoData = $geo['body'] ? json_decode($geo['body'], true) : null;

if (empty($geoData)) {
    echo json_encode([
        'error' => 'Could not locate that city via OpenStreetMap.',
        'debug' => [
            'step' => 'geocode',
            'curl_errno' => $geo['err'],
            'curl_error' => $geo['errMsg'],
            'http_code' => $geo['httpCode'],
            'raw_body_snippet' => $geo['body'] ? substr($geo['body'], 0, 200) : null,
        ],
    ]);
    exit;
}

$lat = (float)$geoData[0]['lat'];
$lon = (float)$geoData[0]['lon'];

// ── Step 2: Overpass query for nearby place nodes ──
$radiusM = $radiusKm * 1000;
$overpassQuery = "[out:json][timeout:20];(node[\"place\"~\"town|village|hamlet\"](around:{$radiusM},{$lat},{$lon}););out body;";

$overpassMirrors = [
    'https://overpass-api.de/api/interpreter',
    'https://overpass.kumi.systems/api/interpreter',
    'https://overpass.openstreetmap.ru/api/interpreter',
];

$overpassResp = null;
$overpassHttpCode = null;
$attempts = [];

foreach ($overpassMirrors as $mirrorUrl) {
    $ch = curl_init($mirrorUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['data' => $overpassQuery]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: HubNicheAuditTool/1.0 (contact@agencyvideopartner.com)',
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $resp = curl_exec($ch);
    $err = curl_errno($ch);
    $errMsg = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $attempts[] = ['mirror' => $mirrorUrl, 'http_code' => $httpCode, 'curl_errno' => $err, 'curl_error' => $errMsg];

    if (!$err && $httpCode === 200 && $resp) {
        $test = json_decode($resp, true);
        if ($test !== null) {
            $overpassResp = $resp;
            $overpassHttpCode = $httpCode;
            break; // stop at first mirror that actually works
        }
    }
    // otherwise fall through and try the next mirror
}

if ($overpassResp === null) {
    echo json_encode([
        'error' => 'All Overpass mirrors failed or timed out — try again in a moment.',
        'debug' => [
            'step' => 'overpass_all_mirrors_failed',
            'attempts' => $attempts,
            'query_sent' => $overpassQuery,
        ],
    ]);
    exit;
}

$overpassData = json_decode($overpassResp, true);
$places = [];

if (!empty($overpassData['elements'])) {
    foreach ($overpassData['elements'] as $el) {
        $name = $el['tags']['name'] ?? null;
        if (!$name) continue;
        // Skip the main city itself if it comes back in results
        if (strtolower($name) === strtolower($city)) continue;

        $pop = isset($el['tags']['population']) ? (int)preg_replace('/[^0-9]/', '', $el['tags']['population']) : null;

        // Haversine distance
        $elLat = $el['lat']; $elLon = $el['lon'];
        $distKm = round(haversine($lat, $lon, $elLat, $elLon), 1);

        $places[] = [
            'name' => $name,
            'population' => $pop,
            'distance_km' => $distKm,
        ];
    }
}

function haversine($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

// Sort: known-population "sweet spot" (<=100k) first, then by distance
usort($places, function($a, $b) {
    $aSweet = ($a['population'] && $a['population'] <= 100000) ? 0 : 1;
    $bSweet = ($b['population'] && $b['population'] <= 100000) ? 0 : 1;
    if ($aSweet !== $bSweet) return $aSweet <=> $bSweet;
    return $a['distance_km'] <=> $b['distance_km'];
});

// Cap results to keep the UI readable
$places = array_slice($places, 0, 25);

echo json_encode([
    'places' => $places,
    'center' => ['lat' => $lat, 'lon' => $lon],
    'debug' => [
        'geocode_http_code' => $geo['httpCode'],
        'overpass_http_code' => $overpassHttpCode,
        'raw_element_count' => count($overpassData['elements'] ?? []),
        'filtered_place_count' => count($places),
        'radius_km' => $radiusKm,
    ],
]);
