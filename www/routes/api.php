<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/utils.php';

Route::middleware(['web'])->group(fn () => [
    Route::get('/playlists', [PlaylistController::class, 'index']),
    Route::post('/playlists', [PlaylistController::class, 'store']),
    Route::get('/playlists/{uuid}', [PlaylistController::class, 'show']),
    Route::put('/playlists/{uuid}', [PlaylistController::class, 'update']),
    Route::delete('/playlists/{uuid}', [PlaylistController::class, 'destroy']),

    Route::get('/songs', [SongController::class, 'index']),
    Route::get('/songs/{uuid}', [SongController::class, 'show']),
    Route::put('/songs/{uuid}', [SongController::class, 'update']),
    Route::delete('/songs/{uuid}', [SongController::class, 'destroy']),

    Route::get('/like', [LikeController::class, 'index']),
    Route::post('/like/{uuid}', [LikeController::class, 'store']),
    Route::get('/like/{uuid}', [LikeController::class, 'show']),
    Route::delete('/like/{uuid}', [LikeController::class, 'destroy']),
    

    Route::get('/users/{id}', [UserController::class, 'show']),
    Route::put('/users/{id}', [UserController::class, 'update']),
    Route::delete('/users/{id}', [UserController::class, 'destroy']),
]);
Route::post('/songs', [SongController::class, 'store'])->middleware(['web']);

Route::get('/me', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        return err(401);
    }

    return ok($user);
})->middleware('auth:sanctum');
