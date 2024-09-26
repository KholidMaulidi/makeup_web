<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\MakeupArtistProfileController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\PackageDetailController;

// Public routes
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::get('top-mua', [MakeupArtistProfileController::class, 'showTopMua']);
Route::get('more-mua', [MakeupArtistProfileController::class, 'showMoreMua']);

// Routes for users
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':1'])->group(function () {
    Route::get('user/profile', [UserProfileController::class, 'showProfile']);
    Route::post('user/profile', [UserProfileController::class, 'updateProfile']);
    Route::post('user/logout', [LogoutController::class, 'logout']);
    Route::post('request/preview', [RequestController::class, 'preview']);
    Route::post('request/create', [RequestController::class, 'create']); 

    Route::get('mua/{id_mua}/schedules', [ScheduleController::class, 'getMuaSchedules']);
    Route::get('mua/{id_mua}/schedules/filtered', [ScheduleController::class, 'filteredSchedules']);
    
    Route::get('mua/{id_mua}/packages', [PackageController::class, 'show_mua_packages']);
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
    Route::get('mua/requests/{id}', [RequestController::class, 'viewRequest']);
    Route::post('request/{id}/approve', [RequestController::class, 'approve']); // MUA can approve requests
    Route::post('request/{id}/reject', [RequestController::class, 'reject']); // MUA can reject requests

    Route::get('mua/schedules', [ScheduleController::class, 'getSchedules']);
    Route::get('mua/mua-packages', [PackageController::class, 'show_mua_packages']);
    Route::get('mua/packages', [PackageController::class, 'index']);
    Route::get('mua/packages/{id}', [PackageController::class, 'show']);
    Route::post('mua/packages', [PackageController::class, 'store']);
    Route::put('mua/packages/{id}', [PackageController::class, 'update']);
    Route::delete('mua/packages/{id}', [PackageController::class, 'destroy']);
    
    Route::post('mua/packages/{package_id}/details', [PackageDetailController::class, 'store']);
    Route::get('mua/packages/{package_id}/details', [PackageDetailController::class, 'showByPackage']);
    Route::put('mua/packages/{package_id}/details', [PackageDetailController::class, 'update']);
    Route::delete('mua/packages/details/{id}', [PackageDetailController::class, 'destroy']);
    
});


