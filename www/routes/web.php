<?php

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})
    ->name('home');

Route::get('/login', function () {
    return Inertia::render('Login');
})
    ->name('login');

Route::get('/logout', function () {
    return Inertia::render('Logout');
})
    ->name('logout');

Route::get('/song/{uuid}', function (Request $request, string $uuid) {
    $song = Song::find($uuid);
    return Inertia::render('Song', ['song' => $song]);
})
    ->whereUuid('uuid')
    ->name('song_detail');

Route::get('/playlist/{uuid}', function (Request $request, string $uuid) {
    $playlist = get_playlist_with_songs($uuid);
    return Inertia::render('Playlist', $playlist);
})
    ->whereUuid('uuid')
    ->name('playlist_detail');
