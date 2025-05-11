<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('schedule');
    }
    return Inertia::render('Book');
})->name('home');
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('schedule', function () {
    return Inertia::render('Schedule');
})->middleware(['auth', 'verified'])->name('schedule');

Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('store');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';
