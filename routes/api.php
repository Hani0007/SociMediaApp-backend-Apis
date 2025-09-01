<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;

// 🔹 Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔹 Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 🔹 Posts
    Route::post('/posts', [PostController::class, 'store']);             // Add post
    Route::get('/posts/{id}', [PostController::class, 'show']);          // Get post by id
    Route::put('/posts/{id}', [PostController::class, 'update']);        // Update post
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);    // Delete post

// 🔹 Media
Route::post('/media', [MediaController::class, 'store']);                  // Upload media
Route::get('/posts/{id}/media', [MediaController::class, 'index']);
Route::get('/media/{id}', [MediaController::class, 'show']); // Get single media by id
        // Get all media of a post
Route::delete('/posts/{postId}/media/{mediaId}', [MediaController::class, 'destroy']);
 // Remove media from a post


    // 🔹 Likes
    Route::post('/like', [LikeController::class, 'store']);              // Like a post
    Route::delete('/like/{id}', [LikeController::class, 'destroy']);     // Unlike a post
    Route::get('/posts/{id}/likes', [LikeController::class, 'index']);   // List likes of a post

    // 🔹 Comments
    Route::post('/comments', [CommentController::class, 'store']);       // Add comment
    Route::put('/comment/{id}', [CommentController::class, 'update']);   // Update comment
    Route::delete('/comment/{id}', [CommentController::class, 'destroy']); // Delete comment
});
