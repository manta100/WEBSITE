<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Resources;
use App\Filament\Pages;

Route::middleware(['web', 'auth:admin'])->prefix('admin')->group(function () {
    Route::get('/', fn () => redirect('/admin/dashboard'));
    
    Route::get('/dashboard', fn () => view('filament.pages.dashboard'))->name('filament.admin.pages.dashboard');
    
    Route::name('filament.admin.resources.')->group(function () {
        Route::prefix('tenants')->group(function () {
            Route::get('/', [Resources\TenantResource::class, 'index'])->name('tenants.index');
            Route::get('/create', [Resources\TenantResource::class, 'create'])->name('tenants.create');
            Route::post('/', [Resources\TenantResource::class, 'store'])->name('tenants.store');
            Route::get('/{record}', [Resources\TenantResource::class, 'view'])->name('tenants.view');
            Route::get('/{record}/edit', [Resources\TenantResource::class, 'edit'])->name('tenants.edit');
            Route::patch('/{record}', [Resources\TenantResource::class, 'update'])->name('tenants.update');
            Route::delete('/{record}', [Resources\TenantResource::class, 'destroy'])->name('tenants.destroy');
        });
        
        Route::prefix('plans')->group(function () {
            Route::get('/', [Resources\PlanResource::class, 'index'])->name('plans.index');
            Route::get('/create', [Resources\PlanResource::class, 'create'])->name('plans.create');
            Route::post('/', [Resources\PlanResource::class, 'store'])->name('plans.store');
            Route::get('/{record}', [Resources\PlanResource::class, 'view'])->name('plans.view');
            Route::get('/{record}/edit', [Resources\PlanResource::class, 'edit'])->name('plans.edit');
            Route::patch('/{record}', [Resources\PlanResource::class, 'update'])->name('plans.update');
            Route::delete('/{record}', [Resources\PlanResource::class, 'destroy'])->name('plans.destroy');
        });
        
        Route::prefix('admins')->group(function () {
            Route::get('/', [Resources\AdminResource::class, 'index'])->name('admins.index');
            Route::get('/create', [Resources\AdminResource::class, 'create'])->name('admins.create');
            Route::post('/', [Resources\AdminResource::class, 'store'])->name('admins.store');
            Route::get('/{record}', [Resources\AdminResource::class, 'view'])->name('admins.view');
            Route::get('/{record}/edit', [Resources\AdminResource::class, 'edit'])->name('admins.edit');
            Route::patch('/{record}', [Resources\AdminResource::class, 'update'])->name('admins.update');
            Route::delete('/{record}', [Resources\AdminResource::class, 'destroy'])->name('admins.destroy');
        });
    });
});
