# Web Traffic Tracker (React Admin + Laravel API)
Small, privacy-aware pageview tracker:

<b>Backend</b>: Laravel 11 (MySQL 8), Eloquent

<b>Admin UI</b>: React (Vite) served inside Laravel

<b>Tracker</b>: tiny JS snippet (sendBeacon/fetch) hitting /api/track

<b>Uniqueness</b>: one row per (visitor × page × UTC day)

<b>Privacy</b>: IP truncated + hashed (optional), host allow-list

<b>DX</b>: seeders, factories, PHPUnit tests, ESLint/Prettier

## Prerequisites
* PHP 8.2+
* Composer 2.x
* Node 18+
* MySQL 8.x
* Optional local server: Herd (or php artisan serve)
* Open ports: 8000 (Laravel dev), 5173 (Vite dev)

## Quick Start
### Clone
* git clone https://github.com/cristikrisch/web-traffic-tracker.git tracker-api
* cd tracker-api

### Install deps
* composer install
* npm install

### Environment
* cp .env.example .env 
* php artisan key:generate

### Create DB in MySQL 

### Configure .env (see section Environment Variables) then migrate + seed data
* php artisan migrate --seed

### Run dev servers
* php artisan serve (or use Heard to serve http(s)://tracker-api.test)
* npm run dev

### Open Admin UI
http://tracker-api.test/admin

## Environment Variables

Update .env with at least:

APP_URL=http://tracker-api.test

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=tracker_api

DB_USERNAME=your_username

DB_PASSWORD=your_password

CORS_ALLOWED_ORIGINS=http://tracker-api.test,http://localhost:5173

TRACK_ALLOWED_HOSTS=localhost,tracker-api.test

IP_HASH_PEPPER=base64:GENERATE_A_LONG_RANDOM_VALUE

ADMIN_USER=your_admin_user

ADMIN_PASS=your_admin_pass

## Embedding the Tracker JS

* Host the file: put public/tracker.min.js (or serve from CDN/S3/Cloud Storage buckets).
* Embed snippet on any site/page you control:
`<script src="https://tracker-api.test/tracker.min.js"
        data-api="https://tracker-api.test/api/track"
        defer></script>`
* data-api must point to your /api/track URL.
* The tracker will:
  * generate a durable visitorKey (localStorage → cookie fallback).
  * send { visitorKey, url, referrer, ts } via sendBeacon or fetch.
  * add bot/DNT checks.
* If your site’s origin differs from the API origin, add it to CORS_ALLOWED_ORIGINS in .env.

## Security · Privacy · Reliability

* CORS limited to your UIs/sites (config/cors.php uses CORS_ALLOWED_ORIGINS)
* Rate limited /api/track
* Host allow-list: only accept url hosts in TRACK_ALLOWED_HOSTS
* IP privacy: store truncated + hashed IP; raw IP optional/off
* Uniqueness enforced by DB unique key
* Admin protected with Basic Auth + security headers

## API

### <b>Base URL (local examples):</b>

Herd: https://tracker-api.test

Artisan: http://127.0.0.1:8000

All endpoints live under /api/* and are CORS-enabled for the origins you configured.

### <b>Conventions</b>

<b>Timestamps:</b> UTC. visited_at is an ISO 8601 datetime; visit_date is YYYY-MM-DD (UTC day).

<b>Uniqueness:</b> one row per (visitor_id, page_id, visit_date).

<b>Rate limiting:</b> /api/track is guarded by throttle:tracking (per-IP + per-visitorKey). Exceeding returns 429.

### 1) Track a page view

`POST /api/track`

Records a single page view. Idempotent per (visitor × page × day).

<b>Headers</b>
* Content-Type: application/json
* X-Vkey: <visitorKey> (optional; sent by fetch fallback; sendBeacon can’t set it)

<b>Body (JSON)</b>

`{
"visitorKey": "string, required",
"url": "https://example.com/path?qs=1",
"referrer": "https://google.com/search?q=...",
"ua": "User-Agent string (optional)",
"ts": 1730544000123,
}
`

<b>Field rules</b>

* visitorKey (required): durable client ID (script generates/stores).
* url (required): full current page URL. Backend canonicalizes it.
* ts (optional): client timestamp (ms since epoch). If omitted, server time used.


<b>The server may skip writes if:</b>

* host not in TRACK_ALLOWED_HOSTS
* request flagged as bot/DNT
* uniqueness constraint already satisfied (same day/visitor/page)

<b>Responses</b>

* 200 OK with minimal JSON: `{ "ok": true }`

### 2) List pages

`GET /api/pages`

Returns known pages (id + canonical URL).

<b>Responses</b>

* 200 OK with JSON: `[
  { "id": 12, "canonical_url": "https://mysite.com/" },
  { "id": 13, "canonical_url": "https://mysite.com/example" }
  ]`

### 3) Unique visits (metrics)

`GET /api/metrics/unique-visits`

Returns daily unique visitors either site-wide or for a single page, for the requested date range.

<b>Query params</b>

* from (required) — YYYY-MM-DD (UTC)
* to (required) — YYYY-MM-DD (UTC), inclusive
* page (optional) — canonical URL to filter by page

<b>Responses</b>

* Site-wide daily uniques (no page param): 
`[
  { "date": "2025-10-27", "uniques": 132 },
  { "date": "2025-10-28", "uniques": 118 },
  { "date": "2025-10-29", "uniques": 141 }
  ]`
* Per-page daily uniques (page provided)
`[
  { "canonical_url": "https://mysite1.com/example", "date": "2025-10-29", "uniques": 42 },
  { "canonical_url": "https://mysite2.com/example2", "date": "2025-10-30", "uniques": 38 }
  ]`
