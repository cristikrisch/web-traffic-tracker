<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    public function uniqueVisits(Request $request)
    {
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(6)->startOfDay();
        $to   = $request->date('to')?->endOfDay() ?? now()->endOfDay();
        $page = $request->string('page')->toString(); // canonical_url optional

        $base = PageVisit::query()
            ->join('pages','pages.id','=','page_visits.page_id')
            ->whereBetween('page_visits.visited_at', [$from, $to]);

        if ($page) {
            // Per-day for a single page
            $rows = (clone $base)
                ->where('pages.canonical_url', $page)
                ->selectRaw('page_visits.visit_date as date, COUNT(DISTINCT page_visits.visitor_id) AS uniques')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json($rows);
        }

        // If no page filter → return per-day per-page (UI can group/sum by page)
        $rows = (clone $base)
            ->selectRaw('pages.canonical_url, page_visits.visit_date as date, COUNT(DISTINCT page_visits.visitor_id) AS uniques')
            ->groupBy('pages.canonical_url', 'date')
            ->orderBy('pages.canonical_url')
            ->orderBy('date')
            ->get();

        return response()->json($rows);
    }
}
