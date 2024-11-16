<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\FollowRequestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetController;
use Illuminate\Support\Facades\Route;

// * Tweets routes
Route::middleware(['auth:api'])->prefix('tweets')->group(function () {
    // Create a tweet
    Route::post('/', [TweetController::class, 'store']);
    // List all tweets by user
    Route::get('/{user}', [TweetController::class, 'view']);
    // Show single tweet
    Route::get('/{tweet}', [TweetController::class, 'show']);
    // Update tweet
    Route::put('/{tweet}', [TweetController::class, 'update']);
    // Delete tweet
    Route::delete('/{tweet}', [TweetController::class, 'destroy']);
});

// * Block Routes
Route::middleware(['auth:api'])->prefix('block')->group(function () {
    // List all blocked users
    Route::get('/', [BlockController::class, 'index']);
    // Block user
    Route::post('/{blockedUser}', [BlockController::class, 'store']);
    // Unblock user
    Route::delete('/{blockedUser}', [BlockController::class, 'destroy']);
});

// * Follow Routes
Route::middleware(['auth:api'])->prefix('follow')->group(function () {
    // Follow user
    Route::post('/{user}', [FollowController::class, 'store']);
    // Unfollow user
    Route::delete('/{user}', [FollowController::class, 'destroy']);
});

// * Follow request routes
Route::middleware(['auth:api'])->prefix('follow-request')->group(function () {
    // List all sent follow requests
    Route::get('/sent', [FollowRequestController::class, 'sent']);
    // List all received follow requests
    Route::get('/received', [FollowRequestController::class, 'received']);
    // Send follow request
    Route::post('/{receiver}', [FollowRequestController::class, 'store']);
    // Cancel follow request
    Route::delete('/{receiver}', [FollowRequestController::class, 'destroy']);
    // Accept follow request
    Route::post('/accept/{sender}', [FollowRequestController::class, 'accept']);
    // Decline follow request
    Route::delete('/decline/{sender}', [FollowRequestController::class, 'decline']);
});

// * Comment routes
Route::middleware(['auth:api'])->prefix('tweets/{tweet}/comments')->group(function () {
    // Get all comments for a tweet
    Route::get('/', [CommentController::class, 'index']);
    // Create a new comment
    Route::post('/', [CommentController::class, 'store']);
});

Route::middleware(['auth:api'])->prefix('comments')->group(function () {
    // Update a comment
    Route::put('/{comment}', [CommentController::class, 'update']);
    // Delete a comment
    Route::delete('/{comment}', [CommentController::class, 'destroy']);
});

// * Like routes
Route::middleware('auth:api')->group(function () {
    // Like a tweet
    Route::post('/tweets/{tweet}/like', [LikeController::class, 'store']);

    // Unlike a tweet
    Route::delete('/tweets/{tweet}/like', [LikeController::class, 'destroy']);

    // View the likers of a tweet
    Route::get('/tweets/{tweet}/likers', [LikeController::class, 'index']);
});

// * Profile routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/{user}', [ProfileController::class, 'show']);
    Route::get('/{user}/followers', [ProfileController::class, 'followers']);
    Route::get('/{user}/following', [ProfileController::class, 'following']);
});