<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PickController;
use App\Http\Controllers\PastRacesController;
use App\Http\Controllers\RulesController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings;


Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/next-race', [PickController::class, 'showResult'])->name('next-race');
    Route::get('/next-race/submit', [PickController::class, 'showSubmit'])->name('next-race.submit');
    Route::post('/submit-picks', [PickController::class, 'submit'])->name('submit-picks');
    Route::get('/past-races', [PastRacesController::class, 'index'])->name('past-races');
    Route::get('/rules', [RulesController::class, 'index'])->name('rules');
    // Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/settings', Settings::class)->name('settings');
});

Route::get('/sms/compliance', function () {
        return view('sms.compliance');
    });
Route::get('/sms/terms-conditions', function () {
        return view('sms.terms-conditions');
    });
Route::get('/sms/privacy-policy', function () {
        return view('sms.privacy-policy');
    });


