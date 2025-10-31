<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MetricsController;

Route::middleware('throttle:tracking')->group(function () {
    Route::post('/track', [TrackController::class, 'store']);
    Route::get('/pages', [PagesController::class, 'index']);
    Route::get('/metrics/unique-visits', [MetricsController::class, 'uniqueVisits']);
});
