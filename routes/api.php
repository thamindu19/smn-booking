<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PoyaDayController;
use App\Http\Controllers\Api\BookingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/poya-days', [PoyaDayController::class, 'index']);
Route::get('/poya-days/{id}', [PoyaDayController::class, 'show']);

Route::get('/bookings', [BookingController::class, 'index'])->middleware(['auth', 'verified']);
Route::get('/bookings/{id}', [BookingController::class, 'show'])->middleware(['auth', 'verified']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::patch('/bookings/{id}/approve', [BookingController::class, 'approve'])->middleware(['auth', 'verified']);
Route::patch('/bookings/{id}/reject', [BookingController::class, 'reject'])->middleware(['auth', 'verified']);
