<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackVisitRequest;
use App\Models\Page;
use App\Models\PageVisit;
use App\Models\Visitor;
use App\Support\UrlTools;
use App\Support\UaTools;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackController extends Controller
{
    public function store(TrackVisitRequest $request)
    {
        $ip = $request->ip();
        $ua = $request->input('ua', $request->userAgent());
        $url = $request->input('url');
        $ref = $request->input('referrer');
        $vkey = $request->input('visitorKey');

        // Normalize URL & UTM
        $canonical = UrlTools::canonical($url);
        $utm = array_filter(array_merge(UrlTools::extractUtm($url), [
            'utm_source'   => data_get($request, 'utm.source'),
            'utm_medium'   => data_get($request, 'utm.medium'),
            'utm_campaign' => data_get($request, 'utm.campaign'),
            'utm_term'     => data_get($request, 'utm.term'),
            'utm_content'  => data_get($request, 'utm.content'),
        ]));

        // Server-side visited time (bounded by now)
        $clientMs = (int) $request->input('ts');
        $visitedAt = now();
        if ($clientMs > 0) {
            $candidate = Carbon::createFromTimestampMsUTC($clientMs);
            // clamp to a reasonable window (±1 day)
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
        // We try insert; if duplicate, ignore.
        try {
            PageVisit::create([
                'visitor_id'  => $visitor->id,
                'page_id'     => $page->id,
                'full_url'    => $url,
                'referrer'    => $ref,
                'utm_source'   => $utm['utm_source']   ?? null,
                'utm_medium'   => $utm['utm_medium']   ?? null,
                'utm_campaign' => $utm['utm_campaign'] ?? null,
                'utm_term'     => $utm['utm_term']     ?? null,
                'utm_content'  => $utm['utm_content']  ?? null,
                'ip'          => $ip,
                'user_agent'  => $ua,
                'visited_at'  => $visitedAt,
            ]);
        } catch (QueryException $e) {
            // Duplicate (unique) — swallow
        }

        return response()->json([
            'ok' => true,
            'visitorKey' => $visitor->visitor_key,
        ]);
    }
}
