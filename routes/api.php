<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Region\DistrictController;
use App\Http\Controllers\Api\Region\ProvinceController;
use App\Http\Controllers\Api\OffDayController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\PackageDetailController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\HistoryRequestController;
use App\Http\Controllers\Api\Region\RegencyController;
use App\Http\Controllers\Api\HistoryTransactionController;
use App\Http\Controllers\Api\MakeupArtistProfileController;

// Public routes
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::get('top-mua', [MakeupArtistProfileController::class, 'showTopMua']);
Route::get('more-mua', [MakeupArtistProfileController::class, 'showMoreMua']);
Route::get('mua/{id_mua}/packages', [PackageController::class, 'show_mua_packages']);
Route::get('mua/{id}/reviews', [ReviewController::class, 'showByMua']);
Route::get('mua/all-galleries', [GalleryController::class, 'show_gallery_user']);
Route::get('make-up-artist/{id}', [MakeupArtistProfileController::class, 'showMuaProfile']);
Route::get('services',[ServiceController::class, 'index']);

Route::get('provinces', [ProvinceController::class, 'index']);
Route::get('regencies', [RegencyController::class, 'index']);
Route::get('districts', [DistrictController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('upload-avatar', [UserController::class, 'updateAvatar']);
});

// Routes for users
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':1'])->group(function () {
    Route::prefix('requests')->group(function () {
        Route::get('profile', [UserProfileController::class, 'showProfile']);
        Route::post('profile', [UserProfileController::class, 'updateProfile']);
    });
    Route::post('logout', [LogoutController::class, 'logout']);
    Route::post('request/show', [RequestController::class, 'show']);
    Route::post('request/store', [RequestController::class, 'create']);

    Route::prefix('review')->group(function () {
        Route::post('{mua_id}', [ReviewController::class, 'store']);
        Route::put('{id}', [ReviewController::class, 'update']);
        Route::delete('{id}', [ReviewController::class, 'destroy']);
    });
    Route::prefix('mua')->group(function () {
        Route::get('{id}/schedules', [ScheduleController::class, 'getMuaSchedules']);
        Route::get('{id}/schedules/filtered', [ScheduleController::class, 'filteredSchedules']);
    });

    
    Route::prefix('user')->group(function () {
        Route::get('history', [HistoryRequestController::class, 'userHistory']);
        Route::get('history/test', [HistoryRequestController::class, 'testMoveToHistory']);
        Route::post('transaction-cancel/{id}', [TransactionController::class, 'requestCancel']);
        Route::get('transactions', [TransactionController::class, 'showUserTransactions']);
        Route::post('transactions/{transactionId}/upload-payment-proof', [TransactionController::class, 'uploadPaymentProof']);
        Route::get('history-transaction', [HistoryTransactionController::class, 'showByUser']);
        Route::get('history-transaction/{id}', [HistoryTransactionController::class, 'show']);
        Route::post('request-cancel/{id}', [RequestController::class, 'requestCancel']);
    });

    Route::get('payment-methods/transaction/{transaction_id}/type/{type_id}', [PaymentMethodController::class, 'showPaymentMethodsByType']);
});

// Routes for admins
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':3'])->prefix('admin')->group(function () {
    Route::get('profile', [UserProfileController::class, 'showProfile']);
    Route::post('logout', [LogoutController::class, 'logout']);
});

// Routes for MUA
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':2'])->prefix('mua')->group(function () {
    Route::get('profile', [MakeupArtistProfileController::class, 'showProfile']);
    Route::post('profile', [MakeupArtistProfileController::class, 'updateProfile']);

    Route::apiResource('galleries', GalleryController::class);

    Route::get('/requests', [RequestController::class, 'viewAllRequests']);
    
    Route::prefix('request')->group(function () {
        Route::get('{id}', [RequestController::class, 'viewRequest']);
        Route::post('{id}/approve', [RequestController::class, 'approve']);
        Route::post('{id}/reject', [RequestController::class, 'reject']);
    });

    Route::prefix('schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'getSchedules']);
        Route::get('filtered', [ScheduleController::class, 'filteredSchedules']);
    });

    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::get('{id}', [PackageController::class, 'show']);
        Route::post('/', [PackageController::class, 'store']);
        Route::put('{id}', [PackageController::class, 'update']);
        Route::delete('{id}', [PackageController::class, 'destroy']);
        Route::post('{id}/image', [PackageController::class, 'uploadImage']);
    });

    Route::prefix('packages-details')->group(function () {
        Route::get('/', [PackageDetailController::class, 'index']);
        Route::get('{id}', [PackageDetailController::class, 'show']);
        Route::post('/', [PackageDetailController::class, 'store']);
        Route::put('{id}', [PackageDetailController::class, 'update']);
        Route::delete('{id}', [PackageDetailController::class, 'destroy']);
    });

    Route::prefix('dayOffs')->group(function () {
        Route::get('/', [OffDayController::class, 'getAllDayOff']);
        Route::post('/', [OffDayController::class, 'setDayOff']);
        Route::put('{id}', [OffDayController::class, 'editDayOff']);
        Route::delete('{id}', [OffDayController::class, 'deleteDayOff']);
    });

    Route::get('history', [HistoryRequestController::class, 'muaHistory']);
    Route::get('transactions', [TransactionController::class, 'showTransactionsByMUA']);
    Route::post('transactions/confirm-payment/{id}', [TransactionController::class, 'confirmPayment']);
    Route::get('history-transaction', [HistoryTransactionController::class, 'showByMua']);

    Route::prefix('payment-methods')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'getAllPaymentMethods']);
        Route::post('/', [PaymentMethodController::class, 'createPaymentMethod']);
        Route::put('{id}', [PaymentMethodController::class, 'updatePaymentMethod']);
        Route::delete('{id}', [PaymentMethodController::class, 'deletePaymentMethod']);
        Route::put('{id}/status', [PaymentMethodController::class, 'updatePaymentMethodStatus']);
    });

    
    Route::prefix('cancel-request')->group(function () {
        Route::get('/', [RequestController::class, 'showCancelRequest']);
        Route::post('/approve/{id}', [RequestController::class, 'approveCancel']);
        Route::post('/reject/{id}', [RequestController::class, 'rejectCancel']);
    });
    
    Route::prefix('cancel-transaction')->group(function () {
        Route::get('/', [TransactionController::class, 'showCancelRequest']);
        Route::post('/approve/{id}', [TransactionController::class, 'approveCancel']);
        Route::post('/reject/{id}', [TransactionController::class, 'rejectCancel']);
    });
    Route::post('logout', [LogoutController::class, 'logout']);
});