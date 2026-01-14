<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Routes that require authentication
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

    Route::get('/song/{uuid}', function ($uuid) {
        return Inertia::render('Song', []);
    })->whereUuid('uuid')
      ->name('song.detail');

    Route::get('/playlist/{uuid}', function ($uuid) {
        return Inertia::render('Playlist', []);
    })->whereUuid('uuid')
      ->name('playlist.detail');

    Route::get('/album/{uuid}', function ($uuid) {
        return Inertia::render('Playlist', []);
    })->whereUuid('uuid')
      ->name('album.detail');

    Route::get('/search/{query}', function (string $query) {
        return Inertia::render('Search', ['query' => $query]);
    })->where('query', '.+')
      ->name('search');

    Route::get('/admin', function () {
        return Inertia::render('Admin');
    })->middleware('can:admin')
      ->name('admin');

});

Route::fallback(function () {
    return Inertia::render('Error', ['status' => 404, 'message' => 'Not Found']);
})->name('fallback');
