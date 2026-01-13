<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    if ($request->user() !== null) {
        return Inertia::render('Home');
    }

    return redirect('/login', 303);
})
    ->name('home');

Route::get('/settings', function (Request $request) {
    if ($request->user() !== null) {
        return Inertia::render('Settings');
    }

    return redirect('/login', 303);
})
    ->name('settings');

Route::get('/queue', function (Request $request) {
    if ($request->user() !== null) {
        return Inertia::render('Queue');
    }

    return redirect('/login', 303);
})
    ->name('Queue');

Route::get('/song/{uuid}', function () {
    return Inertia::render('Song', []);
})
    ->whereUuid('uuid')
    ->name('song.detail');

Route::get('/playlist/{uuid}', function () {
    return Inertia::render('Playlist', []);
})
    ->whereUuid('uuid')
    ->name('playlist.detail');

Route::get('/album/{uuid}', function () {
    return Inertia::render('Playlist', []);
})
    ->whereUuid('uuid')
    ->name('album.detail');

Route::get('/search/{query}', function (Request $request, string $query) {
    return Inertia::render('Search', ['query' => $query]);
})
    ->where('query', '.+')
    ->name('search');



Route::get('/admin', function (Request $request) {
    if ($request->user()->is_admin) {
        return Inertia::render('Admin');
    }

    return Inertia::render('Error', ['status' => 403, 'message' => 'Forbidden']);
})
    ->name('admin');



Route::fallback(function () {
    return Inertia::render('Error', ['status' => 404, 'message' => 'Not Found']);
})
    ->name('fallback');
