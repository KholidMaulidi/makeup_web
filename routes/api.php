<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\MakeupArtistProfileController;
use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Public routes
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

// Routes for users
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':1'])->group(function () {
    Route::get('user/profile', [UserProfileController::class, 'showProfile']);
    Route::post('user/profile', [UserProfileController::class, 'updateProfile']);
    Route::post('user/logout', [LogoutController::class, 'logout']);
    Route::post('request', [RequestController::class, 'create']); // User can create a request
});

// Routes for admins
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':3'])->group(function () {
    Route::get('admin/profile', [UserProfileController::class, 'showProfile']);
    Route::post('admin/logout', [LogoutController::class, 'logout']);
});

// Routes for MUA
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':2'])->group(function () {
    Route::get('mua/profile', [MakeupArtistProfileController::class, 'showProfile']);
    Route::post('mua/profile', [MakeupArtistProfileController::class, 'updateProfile']);
    Route::apiResource('mua/galleries', GalleryController::class);
    Route::post('mua/logout', [LogoutController::class, 'logout']);
    Route::get('mua/requests', [RequestController::class, 'viewAllRequests']);
    Route::post('request/{id}/approve', [RequestController::class, 'approve']); // MUA can approve requests
    Route::post('request/{id}/reject', [RequestController::class, 'reject']); // MUA can reject requests
});


