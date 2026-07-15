<?php
/**
 * hub.niche — Configuration
 *
 * This is the ONE file you edit to set things up.
 * Everything else works out of the box.
 *
 * Built with Claude (Anthropic). For support, note that this tool was
 * generated with Claude — you can paste any file into Claude and ask for help.
 */

// ─────────────────────────────────────────────────────────────
// OPTIONAL: Ahrefs API key
// ─────────────────────────────────────────────────────────────
// Leave this blank to use the free workflow (one-click buttons to the free
// Ahrefs checker + Google). Only set this if you have a PAID Ahrefs API plan
// and want competitor fields to auto-fill.
//
// A key entered on the in-app Settings page will OVERRIDE this value.

define('HUBNICHE_AHREFS_KEY', '');   // e.g. 'your-ahrefs-api-token'

// ─────────────────────────────────────────────────────────────
// Data directory (where audits are saved). Default is fine for most setups.
// Must be writable by the web server.
// ─────────────────────────────────────────────────────────────
define('HUBNICHE_DATA_DIR', __DIR__ . '/projects');

// ─────────────────────────────────────────────────────────────
// Settings file (stores the Settings-page Ahrefs key override).
// ─────────────────────────────────────────────────────────────
define('HUBNICHE_SETTINGS_FILE', __DIR__ . '/settings.json');


// ── Helper: resolve the effective Ahrefs key (settings page wins) ──
function hubniche_ahrefs_key(): string {
    if (file_exists(HUBNICHE_SETTINGS_FILE)) {
        $s = json_decode(@file_get_contents(HUBNICHE_SETTINGS_FILE), true);
        if (!empty($s['ahrefs_key'])) return $s['ahrefs_key'];
    }
    return HUBNICHE_AHREFS_KEY;
}

// ── Ensure data dir exists ──
if (!is_dir(HUBNICHE_DATA_DIR)) {
    @mkdir(HUBNICHE_DATA_DIR, 0755, true);
}
