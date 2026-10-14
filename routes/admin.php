<?php

use Azuriom\Plugin\PlugiitRules\Controllers\Admin\DashboardController;
use Azuriom\Plugin\PlugiitRules\Controllers\Admin\SectionController;
use Azuriom\Plugin\PlugiitRules\Controllers\Admin\SettingController;
use Azuriom\Plugin\PlugiitRules\Controllers\Admin\UserController;
use Azuriom\Plugin\PlugiitRules\Controllers\Admin\VersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('can:plugiit-rules.admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::post('sections/update-order', [SectionController::class, 'updateOrder'])->name('sections.update-order');
    Route::resource('sections', SectionController::class)->except('show');

    Route::resource('versions', VersionController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('settings', [SettingController::class, 'show'])->name('settings');
    Route::post('settings', [SettingController::class, 'save'])->name('settings.save');
});
