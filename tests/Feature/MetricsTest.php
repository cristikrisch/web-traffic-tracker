<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageVisit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_unique_visits_by_day_for_single_page(): void
    {
        $page = Page::factory()->create();
        $visitor = Visitor::factory()->create();

        // Today
        PageVisit::factory()->create([
            'page_id'    => $page->id,
            'visitor_id' => $visitor->id,
            'visited_at' => now('UTC'),
        ]);

        // Yesterday
        PageVisit::factory()->create([
            'page_id'    => $page->id,
            'visitor_id' => $visitor->id,
            'full_url' => 'https://example.com/blog/hello?source=test',
            'visited_at' => now('UTC')->subDay(),
        ]);

        $from = now('UTC')->subDays(1)->toDateString();
        $to   = now('UTC')->toDateString();

        $res = $this->getJson("/api/metrics/unique-visits?from={$from}&to={$to}&page=" . urlencode($page->canonical_url))
            ->assertOk()
            ->json();

        // Expect two day buckets with counts
        $this->assertIsArray($res);
        $this->assertCount(2, $res);
        $this->assertArrayHasKey('date', $res[0]);
        $this->assertArrayHasKey('uniques', $res[0]);
    }

    public function test_returns_unique_visits_by_day_across_all_pages(): void
    {
        $pageA = Page::factory()->create();
        $pageB = Page::factory()->create();

        $visitorA = Visitor::factory()->create();
        $visitorB = Visitor::factory()->create();

        // Visitor A visits page A
        PageVisit::factory()->create([
            'page_id' => $pageA->id,
            'visitor_id' => $visitorA->id,
            'full_url' => 'https://example.com/blog/hello?source=test1',
            'visited_at' => now('UTC'),
        ]);

        // Visitor B visits page A
        PageVisit::factory()->create([
            'page_id' => $pageA->id,
            'visitor_id' => $visitorB->id,
            'full_url' => 'https://example.com/blog/hello?source=test2',
            'visited_at' => now('UTC'),
        ]);

        // Visitor A visits page B
        PageVisit::factory()->create([
            'page_id' => $pageB->id,
            'visitor_id' => $visitorA->id,
            'full_url' => 'https://example.com/blog/hello?source=test3',
            'visited_at' => now('UTC'),
        ]);

        $from = now('UTC')->toDateString();
        $to   = now('UTC')->toDateString();

        $res = $this->getJson("/api/metrics/unique-visits?from={$from}&to={$to}")
            ->assertOk()
            ->json();

        // Expect one day bucket aggregated across pages
        $this->assertIsArray($res);
        $this->assertGreaterThanOrEqual(1, count($res));
        $this->assertArrayHasKey('date', $res[0]);
        $this->assertArrayHasKey('uniques', $res[0]);
        $this->assertGreaterThanOrEqual(2, $res[1]['uniques']); // at least 2 uniques (two pages for visitor A)
    }

}
