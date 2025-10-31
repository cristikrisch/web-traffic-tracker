<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PagesController extends Controller
{
    public function index()
    {
        $pages = Page::query()
            ->orderBy('canonical_url')
            ->limit(1000)
            ->get(['id','canonical_url']);

        return response()->json($pages);
    }
}
