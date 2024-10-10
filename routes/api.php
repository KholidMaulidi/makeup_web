<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\HistoryRequestController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\MakeupArtistProfileController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\OffDayController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Api\PackageDetailController;

// Public routes
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::get('top-mua', [MakeupArtistProfileController::class, 'showTopMua']);
Route::get('more-mua', [MakeupArtistProfileController::class, 'showMoreMua']);

// User see packages route
Route::get('mua/{id_mua}/packages', [PackageController::class, 'show_mua_packages']);
Route::get('make-up-artist/{id}', [MakeupArtistProfileController::class, 'showMuaProfile']);
// Routes for users
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':1'])->group(function () {
    // User Profile Route
    Route::get('user/profile', [UserProfileController::class, 'showProfile']);
    Route::post('user/profile', [UserProfileController::class, 'updateProfile']);

    Route::post('user/logout', [LogoutController::class, 'logout']);
    Route::post('request/show', [RequestController::class, 'show']);
    Route::post('request/store', [RequestController::class, 'create']); 


    // User create request route
    Route::post('request', [RequestController::class, 'create']);

    // User see schedules route
    Route::get('mua/{id_mua}/schedules', [ScheduleController::class, 'getMuaSchedules']);
    Route::get('mua/{id_mua}/schedules/filtered', [ScheduleController::class, 'filteredSchedules']);
    

    // User History
    Route::get('user/history', [HistoryRequestController::class, 'userHistory']);
    Route::get('user/history/test', [HistoryRequestController::class, 'testMoveToHistory']);

    // User Transactions
    Route::get('user/transactions', [TransactionController::class, 'showUserTransactions']);
    Route::post('user/transactions/{transactionId}/upload-payment-proof', [TransactionController::class, 'uploadPaymentProof']);

    // User Payment Method
    Route::get('user/payment-methods/transaction/{transaction_id}/type/{type_id}', [PaymentMethodController::class, 'showPaymentMethodsByType']);

    // User Logout Route
    Route::post('user/logout', [LogoutController::class, 'logout']);
});

// Routes for admins
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':3'])->group(function () {
    // Admin Profile Route
    Route::get('admin/profile', [UserProfileController::class, 'showProfile']);

    // Admin Logout Route
    Route::post('admin/logout', [LogoutController::class, 'logout']);
});

// Routes for MUA
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':2'])->group(function () {
    // MUA Profile Route
    Route::get('mua/profile', [MakeupArtistProfileController::class, 'showProfile']);
    Route::post('mua/profile', [MakeupArtistProfileController::class, 'updateProfile']);

    // Gallery Route
    Route::apiResource('mua/galleries', GalleryController::class);

    // Requests Route
    Route::get('mua/requests', [RequestController::class, 'viewAllRequests']);
    Route::get('mua/requests/{id}', [RequestController::class, 'viewRequest']);
    Route::post('request/{id}/approve', [RequestController::class, 'approve']); // MUA can approve requests
    Route::post('request/{id}/reject', [RequestController::class, 'reject']); // MUA can reject requests

    // Schedules Route
    Route::get('mua/schedules', [ScheduleController::class, 'getSchedules']);

    // Packages Route
    Route::get('mua/mua-packages', [PackageController::class, 'show_mua_packages']);
    Route::get('mua/packages', [PackageController::class, 'index']);
    Route::get('mua/packages/{id}', [PackageController::class, 'show']);
    Route::post('mua/packages', [PackageController::class, 'store']);
    Route::put('mua/packages/{id}', [PackageController::class, 'update']);
    Route::delete('mua/packages/{id}', [PackageController::class, 'destroy']);
    Route::post('mua/packages/{id}/image', [PackageController::class, 'uploadImage']);

    // Package Details Route
    Route::get('mua/packages-details', [PackageDetailController::class, 'index']);
    Route::get('mua/packages-details/{id}', [PackageDetailController::class, 'show']);
    Route::post('mua/packages-details', [PackageDetailController::class, 'store']);
    Route::put('mua/packages-details/{id}', [PackageDetailController::class, 'update']);
    Route::delete('mua/packages-details/{id}', [PackageDetailController::class, 'destroy']);
    

    Route::resource('mua/packages', PackageController::class);

    // DayOff Route
    Route::get('mua/dayOffs', [OffDayController::class, 'getAllDayOff']);
    Route::post('mua/dayOff', [OffDayController::class, 'setDayOff']);
    Route::put('mua/dayoff/{id}', [OffDayController::class, 'editDayOff']);
    Route::delete('mua/dayoff/{id}', [OffDayController::class, 'deleteDayOff']);

    // MUA History
    Route::get('mua/history', [HistoryRequestController::class, 'muaHistory']);

    // MUA Transactions
    Route::post('/mua/transactions/confirm-payment/{id}', [TransactionController::class, 'confirmPayment']);
    Route::get('/mua/transactions', [TransactionController::class, 'showTransactionsByMUA']);

    // MUA Payment Method
    Route::get('mua/payment-methods', [PaymentMethodController::class, 'getAllPaymentMethods']);
    Route::post('mua/payment-methods', [PaymentMethodController::class, 'createPaymentMethod']);
    Route::put('mua/payment-methods/{id}', [PaymentMethodController::class, 'updatePaymentMethod']);
    Route::delete('mua/payment-methods/{id}', [PaymentMethodController::class, 'deletePaymentMethod']);
    Route::put('/payment-method/{id}/status', [PaymentMethodController::class, 'updatePaymentMethodStatus']);

    // MUA Off Day Route
    Route::get('mua/off-days', [OffDayController::class, 'getAllOffDays']);
    Route::post('mua/off-day', [OffDayController::class, 'setOffDay']);
    Route::put('mua/off-day/{id}', [OffDayController::class, 'editOffDay']);
    Route::delete('mua/off-day/{id}', [OffDayController::class, 'deleteOffDay']);
    
    // MUA Logout Route
    Route::post('mua/logout', [LogoutController::class, 'logout']);

});


