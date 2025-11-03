<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Visitor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 10 unique pages and 30 unique visitors
        Page::factory()->count(10)->create();
        Visitor::factory()->count(30)->create();

        // 200 random visits in the last 7 days
        $pages = Page::pluck('id');
        $vis   = Visitor::pluck('id');

        $rows = [];
        for ($i=0; $i<200; $i++) {
            $day = now('UTC')->subDays(rand(0,6))->toDateString();
            $rows[] = [
                'page_id'    => $pages->random(),
                'visitor_id' => $vis->random(),
                'visited_at' => $day.' 12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('page_visits')->insertOrIgnore($rows);
    }
}
