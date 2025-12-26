<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReportController;
use App\Models\AppUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Public Routes
Route::post('/auth/guest', [AuthController::class, 'guestLogin']);
Route::post('/auth/social', [AuthController::class, 'socialLogin']);

// Protected Routes (Require Token)
Route::middleware('auth:sanctum')->group(function () {

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