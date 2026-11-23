<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\ViewingRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Khmer Home AI REST API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Authentication Public Routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Public Properties & Amenities
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/{idOrSlug}', [PropertyController::class, 'show']);
    Route::get('amenities', [PropertyController::class, 'amenities']);

    // Public AI Endpoints
    Route::prefix('ai')->group(function () {
        Route::post('smart-search', [AiController::class, 'smartSearch']);
        Route::get('recommendations', [AiController::class, 'recommendations']);
        Route::post('rental-assistant', [AiController::class, 'rentalAssistant']);
    });

    // Authenticated Endpoints (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth & Profile
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'user']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
        });

        // Property Management (Landlord / Admin)
        Route::post('properties', [PropertyController::class, 'store']);
        Route::put('properties/{id}', [PropertyController::class, 'update']);
        Route::delete('properties/{id}', [PropertyController::class, 'destroy']);

        // Favorites
        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites/{propertyId}/toggle', [FavoriteController::class, 'toggle']);

        // Viewing Requests & Bookings
        Route::get('viewing-requests', [ViewingRequestController::class, 'index']);
        Route::post('viewing-requests', [ViewingRequestController::class, 'store']);
        Route::put('viewing-requests/{id}/status', [ViewingRequestController::class, 'updateStatus']);

        // Chat & Messages
        Route::get('messages/conversations', [MessageController::class, 'conversations']);
        Route::get('messages/conversation/{otherUserId}', [MessageController::class, 'conversation']);
        Route::post('messages', [MessageController::class, 'store']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Authenticated AI Tools
        Route::prefix('ai')->group(function () {
            Route::post('generate-description', [AiController::class, 'generateDescription']);
            Route::post('check-completeness', [AiController::class, 'checkCompleteness']);
            Route::post('analyze-photo', [AiController::class, 'analyzePhoto']);
            Route::post('duplicate-check', [AiController::class, 'duplicateCheck']);
        });

        // Admin Management
        Route::prefix('admin')->group(function () {
            Route::get('dashboard', [AdminController::class, 'dashboard']);
            Route::get('users', [AdminController::class, 'users']);
            Route::put('users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);
            Route::put('properties/{id}/status', [AdminController::class, 'updatePropertyStatus']);
        });
    });
});
