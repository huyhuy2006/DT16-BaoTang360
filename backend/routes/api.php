<?php

use App\Http\Controllers\Api\ArtifactController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\TourProgressController;
use App\Http\Controllers\Api\CollectionItemController;
use App\Http\Controllers\Api\GuestbookController;
use App\Http\Controllers\Api\HotspotController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\NarrationController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\SceneController;
use App\Http\Controllers\Api\SceneLinkController;
use App\Http\Controllers\Api\SpaceController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VisitLogController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/forgot-password', [PasswordController::class, 'forgot'])->middleware('throttle:5,1');
Route::post('/reset-password', [PasswordController::class, 'reset'])->middleware('throttle:10,1');

Route::get('/spaces', [SpaceController::class, 'index']);
Route::get('/spaces/{space}', [SpaceController::class, 'show']);
Route::get('/scenes', [SceneController::class, 'index']);
Route::get('/scenes/{scene}', [SceneController::class, 'show']);
Route::get('/artifacts', [ArtifactController::class, 'index']);
Route::get('/artifacts/{artifact}', [ArtifactController::class, 'show']);
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/narrations', [NarrationController::class, 'index']);
Route::get('/tours', [TourController::class, 'index']);
Route::get('/tours/{tour}', [TourController::class, 'show']);
Route::get('/recommendations', [RecommendationController::class, 'index']);
Route::get('/feedback', [FeedbackController::class, 'index']);
Route::get('/guestbook', [GuestbookController::class, 'index']);
Route::post('/guestbook', [GuestbookController::class, 'store'])->middleware('throttle:10,1');
Route::post('/visit-logs', [VisitLogController::class, 'store'])->middleware('throttle:60,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);
    Route::get('/feedback/mine', [FeedbackController::class, 'mine']);
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{targetType}/{targetId}', [FavoriteController::class, 'destroy']);
    Route::get('/tour-progress/{tour}', [TourProgressController::class, 'show']);
    Route::put('/tour-progress/{tour}', [TourProgressController::class, 'update']);
    Route::post('/change-password', [PasswordController::class, 'changePassword']);
    Route::get('/collection-items', [CollectionItemController::class, 'index']);
    Route::post('/collection-items', [CollectionItemController::class, 'store']);
    Route::delete('/collection-items/{collectionItem}', [CollectionItemController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:admin,editor'])->group(function () {
    Route::post('/spaces', [SpaceController::class, 'store']);
    Route::put('/spaces/{space}', [SpaceController::class, 'update']);
    Route::delete('/spaces/{space}', [SpaceController::class, 'destroy']);
    Route::post('/scenes', [SceneController::class, 'store']);
    Route::put('/scenes/{scene}', [SceneController::class, 'update']);
    Route::delete('/scenes/{scene}', [SceneController::class, 'destroy']);
    Route::post('/scene-links', [SceneLinkController::class, 'store']);
    Route::delete('/scene-links/{sceneLink}', [SceneLinkController::class, 'destroy']);
    Route::post('/artifacts', [ArtifactController::class, 'store']);
    Route::put('/artifacts/{artifact}', [ArtifactController::class, 'update']);
    Route::delete('/artifacts/{artifact}', [ArtifactController::class, 'destroy']);
    Route::post('/hotspots', [HotspotController::class, 'store']);
    Route::put('/hotspots/{hotspot}', [HotspotController::class, 'update']);
    Route::delete('/hotspots/{hotspot}', [HotspotController::class, 'destroy']);
    Route::post('/languages', [LanguageController::class, 'store']);
    Route::post('/narrations', [NarrationController::class, 'store']);
    Route::delete('/narrations/{narration}', [NarrationController::class, 'destroy']);
    Route::post('/tours', [TourController::class, 'store']);
    Route::put('/tours/{tour}', [TourController::class, 'update']);
    Route::delete('/tours/{tour}', [TourController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/visit-logs/summary', [VisitLogController::class, 'summary']);
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{user}/role', [UserController::class, 'updateRole']);
    Route::put('/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::get('/feedback/moderation', [FeedbackController::class, 'adminIndex']);
    Route::put('/feedback/{feedback}/status', [FeedbackController::class, 'moderate']);
});
