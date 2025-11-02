<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return view('welcome');});

Route::middleware(['admin.basic','sec.headers'])->group(function () {
    Route::view('/admin/{any?}', 'admin')->where('any', '.*');
});
