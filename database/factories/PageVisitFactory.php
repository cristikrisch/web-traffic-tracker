<?php
namespace Database\Factories;

use App\Models\Page;
use App\Models\PageVisit;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageVisitFactory extends Factory {
    protected $model = PageVisit::class;
    public function definition(): array
    {
        return [
            'page_id'    => Page::factory(),
            'visitor_id' => Visitor::factory(),
            'visited_at' => now('UTC'),
            'full_url'   => $this->faker->unique()->url(),
        ];
    }
}
