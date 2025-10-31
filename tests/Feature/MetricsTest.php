<?php

namespace Tests\Feature;

use Tests\TestCase;

class MetricsTest extends TestCase
{
    public function test_unique_visits_endpoint_returns_counts(): void
    {

        $res = $this->getJson('/api/metrics/unique-visits?from=2025-10-01&to=2025-10-02&page=https%3A%2F%2Fexample.com%2Fblog%2Fhello');
        $res->assertOk()->assertJsonStructure([['date','uniques']]);
    }
}
