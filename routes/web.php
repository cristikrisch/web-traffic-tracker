<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return view('welcome');});

Route::middleware('admin.basic')->group(function () {
    Route::view('/admin/{any?}', 'admin')->where('any', '.*');
});
