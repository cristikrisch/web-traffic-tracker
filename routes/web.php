<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return view('welcome');});
Route::get('/test', function () {return view('test');});

// Basic auth for admin dashboard
Route::middleware(['admin.basic','sec.headers'])->group(function () {
    Route::view('/admin/{any?}', 'admin')->where('any', '.*');
});
