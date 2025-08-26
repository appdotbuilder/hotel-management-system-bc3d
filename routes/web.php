<?php

use App\Http\Controllers\HotelDashboardController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Hotel Management Dashboard - Main page
    Route::controller(HotelDashboardController::class)->group(function () {
        Route::get('/hotel-dashboard', 'index')->name('hotel-dashboard.index');
        Route::post('/hotel-dashboard', 'store')->name('hotel-dashboard.store');
    });

    // Reservations Management
    Route::resource('reservations', ReservationController::class);

    // Original dashboard redirect
    Route::get('dashboard', function () {
        return redirect()->route('hotel-dashboard.index');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
