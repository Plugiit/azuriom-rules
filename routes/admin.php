<?php

use Azuriom\Plugin\Rules\Controllers\Admin\DashboardController;
use Azuriom\Plugin\Rules\Controllers\Admin\SectionController;
use Azuriom\Plugin\Rules\Controllers\Admin\SettingController;
use Azuriom\Plugin\Rules\Controllers\Admin\UserController;
use Azuriom\Plugin\Rules\Controllers\Admin\VersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('can:rules.admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::post('sections/update-order', [SectionController::class, 'updateOrder'])->name('sections.update-order');
    Route::resource('sections', SectionController::class)->except('show');

    Route::resource('versions', VersionController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('settings', [SettingController::class, 'show'])->name('settings');
    Route::post('settings', [SettingController::class, 'save'])->name('settings.save');
});
