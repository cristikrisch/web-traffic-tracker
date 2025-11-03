<?php
namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory {

    protected $model = Page::class;

    public function definition(): array
    {
        $url = $this->faker->unique()->url();
        return ['canonical_url' => parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST).'/demo'];
    }
}
