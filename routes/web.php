<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web'])->group(function () {
    Route::get('/intranet/{any?}', function () {
        return view('app');
    })->where('any', '.*');
});

Route::get('/{vue_capture?}', function () {
    return view('web');
})->where('vue_capture', '[\/\w\.-]*');
