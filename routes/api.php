<?php

use App\Models\PageVisit;
use App\Models\Visitor;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MetricsController;

// Tracking endpoint behind throttle protection
Route::middleware('throttle:tracking')->group(function () {
    Route::post('/track', [TrackController::class, 'store']);
});

Route::get('/pages', [PagesController::class, 'index']);
Route::get('/metrics/unique-visits', [MetricsController::class, 'uniqueVisits']);

// Protect DSAR endpoints under basic auth
Route::middleware('admin.basic')->group(function () {
    Route::delete('/visitor/{key}', function(string $key) {
        $v = Visitor::where('visitor_key',$key)->first();
        if (!$v)
            return response()->json(['deleted'=>0]);
        $count = PageVisit::where('visitor_id',$v->id)->delete();
        $v->delete();
        return response()->json(['deleted'=>$count+1]);
    });

    Route::get('/visitor/{key}/export', function(string $key) {
        $v = Visitor::where('visitor_key',$key)->firstOrFail();
        $visits = PageVisit::with('page:id,canonical_url')
            ->where('visitor_id',$v->id)->orderBy('visited_at')->get();
        return response()->json(['visitorKey'=>$key,'visits'=>$visits]);
    });
});
