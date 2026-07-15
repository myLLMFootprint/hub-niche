# hub.niche

**A self-hosted PHP tool for scoring local service niche + city opportunities.**

Pick a market, name a local service (gutters, septic, tree work, etc.), check who's ranking, and get a weighted 0–100 opportunity score with a plain-language verdict — saved to persistent history on your own server. No database, no API keys, no build step.

> Built with [Claude](https://claude.ai) (Anthropic). For help or to extend it, paste any file into Claude and describe what you need.

---

## What it does

You choose a state and city, name a service, and check the top three sites currently ranking for your target keyword. hub.niche weights it into a single score across three factors:

- **Service fit** (30%) — how well the niche suits a local lead-gen model
- **City size** (20%) — favoring mid-size markets
- **Competitor beatability** (50%) — how weak the ranking sites are

It favors phone-driven home services in markets where incumbents haven't invested in SEO. It's a decision aid — always sanity-check search demand before committing.

---

## Requirements

- **PHP 7.4+** (standard cPanel/Apache shared hosting is fine)
- **cURL** extension enabled (default almost everywhere) — used for the nearby-city lookup
- A way to upload files (cPanel File Manager, FTP, or SSH)
- **No database. No API keys.** (Optional paid Ahrefs API key for auto-fill — everything works without it.)

---

## Install

### Option A — Download ZIP
1. Click **Code -> Download ZIP** above
2. Unzip and upload the files into a `hub.niche/` folder in your web root, e.g. `/public_html/hub.niche/`
3. Create a `projects/` folder inside `hub.niche/` and set permissions to `755`
4. Visit `https://yourdomain.com/hub.niche/` — the landing page runs a live check (PHP / cURL / folder perms)

### Option B — Clone
```bash
cd /path/to/public_html
git clone https://github.com/myLLMFootprint/hub-niche.git hub.niche
mkdir -p hub.niche/projects && chmod 755 hub.niche/projects
```

Then open `https://yourdomain.com/hub.niche/`.

---

## Optional: Ahrefs API key

Two ways — the Settings page always overrides the config file:

- **config.php:** `define('HUBNICHE_AHREFS_KEY', 'your-token');`
- **Settings page:** paste the key in the app, no file editing

Leave blank to use the free workflow (one-click buttons to the free Ahrefs checker + Google).

---

## Using it

1. **Pick a market** — state, then main city (or a smaller nearby city, often weaker competition)
2. **Name the service** — keyword suggestions appear as you type
3. **See who's ranking** — one click opens Google for your keyword
4. **Measure each competitor** — free [Ahrefs checker](https://ahrefs.com/website-authority-checker) for Domain Rating + backlinks; `site:domain.com` for page count. Blank fields don't affect the score.
5. **Save & score** — get a verdict, saved to History

---

## Files

| File | Purpose |
|------|---------|
| `index.php` | Read First landing page (intro, requirements, live check) |
| `research.php` | New Audit — start here |
| `history.php` | Saved-audit history |
| `settings.php` | Optional Ahrefs key |
| `config.php` | The one file you edit |
| `audit-form.php` | Niche, keyword, competitor entry |
| `audit-save.php` | Scores & saves an audit |
| `view-audit.php` | Full report page |
| `niche-criteria.php` | Scoring logic |
| `nearby-cities.php` | Nearby-town lookup (OpenStreetMap) |
| `nav.php` | Shared navigation |
| `create.php`, `setup.php` | Redirects (backwards compatibility) |

Audits are saved as JSON under `projects/`.

---

## Notes & limitations

- **Nearby-city lookup** uses free OpenStreetMap servers, which occasionally rate-limit or time out. Everything else works regardless.
- **No search-volume data** — check Google Keyword Planner (free) before committing to a keyword.
- **Subdomain distortion** — a competitor on a platform subdomain (e.g. a Shopify store) may return the platform's Domain Rating, not their own. Leave DA blank in that case.
- **Scores aren't retroactive** — saved audits are snapshots; changing the logic doesn't re-score old records.

---

## License

MIT — free to use, modify, and share.

## Support

Built with **Claude** (Anthropic). Paste any file into Claude to get help extending or fixing it. Bug reports welcome in [Issues](https://github.com/myLLMFootprint/hub-niche/issues).
