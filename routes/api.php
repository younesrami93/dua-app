<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReportController;
use App\Models\AppUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;



Route::middleware(['throttle:6,1', 'app.secret'])->group(function () {

    // Public Routes
    Route::post('/auth/guest', [AuthController::class, 'guestLogin']);
    Route::post('/auth/social', [AuthController::class, 'socialLogin']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    //Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    //Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
});


// Protected Routes (Require Token)
Route::middleware(['auth:sanctum', 'user.status'])->group(function () {


    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Test if token works
    Route::get('/user', function () {
        return auth()->user();
    });


    // post routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);

    // interaction routes
    Route::post('/posts/{id}/like', [InteractionController::class, 'toggleLike']);
    Route::post('/posts/{id}/share', [InteractionController::class, 'share']);

    // comment routes
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);

    // report routes
    Route::post('/report', [ReportController::class, 'store']);

    Route::get('/categories', function () {
        return response()->json(\App\Models\Category::all());
    });

    Route::get('/users/{id}', function ($id) {
        return AppUser::findOrFail($id);
    });
});