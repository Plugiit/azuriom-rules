<?php

use Azuriom\Plugin\Rules\Controllers\RulesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [RulesController::class, 'index'])->name('index');
Route::get('/versions/{version}', [RulesController::class, 'show'])->name('versions.show');
Route::post('/accept', [RulesController::class, 'accept'])->middleware('auth')->name('accept');
