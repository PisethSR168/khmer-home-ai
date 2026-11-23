<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\WebAdminController;
use App\Http\Controllers\Web\WebAiController;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\WebLandlordController;
use App\Http\Controllers\Web\WebPropertyController;
use App\Http\Controllers\Web\WebRenterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Khmer Home AI Platform
|--------------------------------------------------------------------------
*/

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/properties', [WebPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{slug}', [WebPropertyController::class, 'show'])->name('properties.show');

// AI Smart Search & Concierge
Route::get('/ai/search', [WebAiController::class, 'smartSearch'])->name('ai.search');
Route::get('/ai/assistant', [WebAiController::class, 'assistant'])->name('ai.assistant');

// Authentication
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Renter Dashboard & Actions
    Route::get('/renter/dashboard', [WebRenterController::class, 'dashboard'])->name('renter.dashboard');
    Route::post('/favorites/{propertyId}/toggle', [WebRenterController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::post('/viewings/request', [WebRenterController::class, 'requestViewing'])->name('viewings.request');

    // Landlord Dashboard & Property Management
    Route::prefix('landlord')->name('landlord.')->group(function () {
        Route::get('/dashboard', [WebLandlordController::class, 'dashboard'])->name('dashboard');
        Route::get('/properties/create', [WebLandlordController::class, 'createProperty'])->name('properties.create');
        Route::post('/properties', [WebLandlordController::class, 'storeProperty'])->name('properties.store');
        Route::get('/viewings', [WebLandlordController::class, 'viewings'])->name('viewings');
        Route::put('/viewings/{id}/status', [WebLandlordController::class, 'updateViewingStatus'])->name('viewings.status');
        Route::delete('/properties/{id}', [WebLandlordController::class, 'deleteProperty'])->name('properties.delete');
    });

    // Admin Dashboard
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [WebAdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/users/{id}/toggle', [WebAdminController::class, 'toggleUser'])->name('users.toggle');
        Route::post('/properties/{id}/featured', [WebAdminController::class, 'toggleFeatured'])->name('properties.featured');
        Route::post('/landlords/{id}/verify', [WebAdminController::class, 'verifyLandlord'])->name('landlords.verify');
    });
});
