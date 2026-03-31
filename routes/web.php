<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'tenant'])->prefix('{domain?}')->group(function () {
    Route::middleware('trial.check')->group(function () {
        Route::get('pos', [HomeController::class, 'pos'])->name('pos');
        Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    });
    
    Route::get('subscription', [HomeController::class, 'subscription'])->name('subscription');
    Route::post('subscription', [HomeController::class, 'subscribe'])->name('subscription.subscribe');
});

Route::get('/health', fn () => response()->json(['status' => 'ok']));
