<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackVisitRequest;
use App\Models\Page;
use App\Models\Visitor;
use App\Support\IpTools;
use App\Support\UrlTools;
use App\Support\UaTools;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrackController extends Controller
{
    public function store(TrackVisitRequest $request)
    {
        $url = $request->input('url');
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;

        // Check allowed hosts first
        $allowed = config('tracker.allowed_hosts');
        if ($host && $allowed !== ['*'] && !in_array($host, $allowed, true)) {
            return response()->json(['ok'=>false,'skipped'=>'host_not_allowed'], 403);
        }

        $rawIp = $request->ip();
        // Truncate and Hash the IP for PII compliance
        $ipTrunc = IpTools::truncate($rawIp);
        $ipHash  = config('tracker.hash_ip') ? IpTools::hash($rawIp, config('tracker.ip_hash_pepper')) : null;

        $ua = $request->input('ua', $request->userAgent());
        $ref = $request->input('referrer');
        $vkey = $request->input('visitorKey');

        // Normalize URL
        $canonical = UrlTools::canonical($url);

        // Server-side visited time (bounded by now)
        $clientMs = (int) $request->input('ts');
        $visitedAt = now();
        if ($clientMs > 0) {
            $candidate = Carbon::createFromTimestampMsUTC($clientMs);
            // Clamp to a reasonable window (±1 day)
            if ($candidate->between(now()->subDay(), now()->addDay())) {
                $visitedAt = $candidate;
            }
        }

        // Upsert visitor
        $visitor = DB::transaction(function () use ($vkey, $ua) {
            $uaHash = UaTools::hash($ua);
            if ($vkey) {
                $visitor = Visitor::firstOrCreate(
                    ['visitor_key' => $vkey],
                    ['user_agent_hash' => $uaHash, 'first_seen_at' => now(), 'last_seen_at' => now()]
                );
            } else {
                $vkeyGen = bin2hex(random_bytes(8)) . now()->timestamp;
                $visitor = Visitor::create([
                    'visitor_key' => $vkeyGen,
                    'user_agent_hash' => $uaHash,
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                ]);
            }
            $visitor->update(['last_seen_at' => now()]);
            return $visitor;
        });

        // Upsert page
        $page = Page::firstOrCreate(['canonical_url' => $canonical]);

        // Enforce uniqueness (visitor+page+day) with DB unique index.
        DB::table('page_visits')->insertOrIgnore([[
            'visitor_id'  => $visitor->id,
            'page_id'     => $page->id,
            'full_url'    => $url,
            'referrer'    => $ref,
            'ip'          => config('tracker.store_raw_ip') ? $rawIp : null,
            'ip_trunc'    => $ipTrunc,
            'ip_hash'     => $ipHash,
            'user_agent'  => $ua,
            'visited_at'  => $visitedAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        return response()->json([
            'ok' => true,
            'visitorKey' => $visitor->visitor_key,
        ], 200);
    }
}
