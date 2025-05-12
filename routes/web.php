<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
