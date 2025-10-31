<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TrackVisitTest extends TestCase
{
    public function test_it_creates_unique_visit_per_day(): void
    {
        $payload = [
            'visitorKey' => 'abc123',
            'url' => 'https://example.com/blog/hello?utm_source=test',
            'referrer' => 'https://google.com',
            'ua' => 'Mozilla/5.0',
        ];

        $this->postJson('/api/track', $payload)->assertOk();
        $this->postJson('/api/track', $payload)->assertOk(); // duplicate same day

        $this->assertDatabaseCount('page_visits', 1);
    }
}
