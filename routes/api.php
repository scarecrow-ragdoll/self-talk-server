<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Authentication is handled by Laravel Sanctum.
| Public routes: register, login
| Protected routes: everything else — requires a valid Bearer token.
|
*/

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

// ── Protected routes ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Users
    Route::get('users', [UserController::class, 'index']);

    // Rooms
    Route::get('rooms',  [RoomController::class, 'index']);
    Route::post('rooms', [RoomController::class, 'store']);

    // Messages  (nested under rooms)
    Route::get('rooms/{room}/messages',  [MessageController::class, 'index']);
    Route::post('rooms/{room}/messages', [MessageController::class, 'store']);
});
