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

Route::get('/login', function () {
    return Inertia::render('Login');
})
    ->name('login');

Route::get('/song/{uuid}', function () {
    return Inertia::render('Song', []);
})
    ->whereUuid('uuid')
    ->name('song.detail');

Route::get('/{playlist}/{uuid}', function () {
    return Inertia::render('Playlist', []);
})
    ->where('playlist', '/(playlist|album)/')
    ->whereUuid('uuid')
    ->name('playlist.detail');
