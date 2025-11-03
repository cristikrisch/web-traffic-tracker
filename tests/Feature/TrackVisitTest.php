<?php

namespace Tests\Feature;

use App\Models\PageVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackVisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_unique_visit_per_day(): void
    {
        $payload = [
            'visitorKey' => 'abc123',
            'url' => 'https://example.com/blog/hello?source=test',
            'referrer' => 'https://google.com',
            'ua' => 'Mozilla/5.0',
        ];

        $this->postJson('/api/track', $payload)->assertOk();
        $this->postJson('/api/track', $payload)->assertOk(); // Duplicate same day

        $this->assertDatabaseCount('page_visits', 1); // Page visits should count just one

        $visit = PageVisit::first();
        $this->assertNotNull($visit);
        $this->assertEquals('https://example.com/blog/hello?source=test', $visit->full_url);
        $this->assertNotNull($visit->visited_at);
    }

    public function test_host_allow_list_blocks_unknown_sites(): void
    {
        // Only allow known hosts
        config()->set('tracker.allowed_hosts', ['tracker-api.test']);

        $payload = [
            'visitorKey' => 'v_abc123',
            'url'        => 'https://evil.com/page',
            'ua'         => 'Mozilla',
        ];

        $this->postJson('/api/track', $payload)->assertOk();

        $this->assertDatabaseCount('page_visits', 0);
    }

    public function test_ip_is_truncated_and_hashed_when_enabled(): void
    {
        // Ensure IP privacy is on for the test
        config()->set('tracker.store_raw_ip', false);
        config()->set('tracker.hash_ip', true);
        config()->set('tracker.ip_hash_pepper', 'test-pepper');

        $payload = [
            'visitorKey' => 'v_abc123',
            'url'        => 'https://example.com/page',
            'ua'         => 'Mozilla',
        ];

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.45']);

        $this->postJson('/api/track', $payload)->assertSuccessful();

        /** @var PageVisit $visit */
        $visit = PageVisit::first();
        $this->assertNotNull($visit);
        $this->assertNull($visit->ip); // Raw IP should not stored and hashed and truncated fields should populate
        $this->assertSame('203.0.113.0', $visit->ip_trunc);
        $this->assertNotNull($visit->ip_hash);
        $this->assertSame(64, strlen($visit->ip_hash));
    }
}
