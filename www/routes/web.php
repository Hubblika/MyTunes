<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaylistSongController;
use App\Http\Controllers\StreamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require_once __DIR__.'/utils.php';

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');

    Route::get('/queue', function () {
        return Inertia::render('Queue');
    })->name('queue');

    Route::get('/playlist/{uuid}', function ($uuid) {
        return Inertia::render('Playlist', ['uuid' => $uuid]);
    })->whereUuid('uuid')
      ->name('playlist.detail');

    Route::get('/admin', function () {
        return Inertia::render('Admin');
    })->middleware('can:admin')
      ->name('admin');


    Route::get('/playlists', [PlaylistController::class, 'index']);
    Route::post('/playlists', [PlaylistController::class, 'store']);
    Route::get('/playlists/{uuid}', [PlaylistController::class, 'show']);
    Route::put('/playlists/{uuid}', [PlaylistController::class, 'update']);
    Route::delete('/playlists/{uuid}', [PlaylistController::class, 'destroy']);

    Route::get('/playlists/{playlist}/songs', [PlaylistSongController::class, 'index']);
    Route::post('/playlists/{playlist}/songs', [PlaylistSongController::class, 'store']);
    Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistSongController::class, 'destroy']);

    Route::get('/songs', [SongController::class, 'index']);
    Route::post('/songs', [SongController::class, 'store']);
    Route::get('/songs/{uuid}', [SongController::class, 'show']);
    Route::put('/songs/{uuid}', [SongController::class, 'update']);
    Route::delete('/songs/{uuid}', [SongController::class, 'destroy']);

    Route::get('/likes', [LikeController::class, 'index']);
    Route::post('/likes/{uuid}', [LikeController::class, 'store']);
    Route::get('/likes/{uuid}', [LikeController::class, 'show']);
    Route::delete('/likes/{uuid}', [LikeController::class, 'destroy']);

    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/search', function (Request $request) {
    $query = $request->input('query', '');
    return Inertia::render('Search', ['query' => $query]);
})->name('search');

    Route::get('/stream/{song}', [StreamController::class, 'stream'])->name('songs.stream');

    Route::get('/me', fn (Request $request) => ok($request->user()));
});

Route::fallback(function () {
    return Inertia::render('Error', ['status' => 404, 'message' => 'Not Found']);
})->name('fallback');
