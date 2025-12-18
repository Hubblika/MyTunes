<?php

use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/utils.php';



Route::middleware([])->group(fn () => [
    Route::post('/song', function (Request $request) {
        $user = $request->user();

        if (!$user) {
            return err(401);
        }

        $audio = $request->file('audio.mp3');
        $cover = $request->file('cover.png');

        if (!$audio) {
            return err(400, ['field' => 'audio']);
        }

        $uuid = uuid_create();

        Storage::disk('local')->put('uploads/songs/{$uuid}/.txt', file_get_contents($audio));
        Storage::disk('local')->put('uploads/songs/{$uuid}/cover.png', file_get_contents($cover));

        return ok(1);
    })
        ->name('api.song.new'),

    Route::get('/song/{uuid}', function (Request $request, string $uuid) {
        $song = Song::find($uuid);

        if (!$song) {
            return err(404);
        }

        return ok($song);
    })
        ->whereUuid('uuid')
        ->name('api.song.by-uuid'),



    Route::post('/playlist', function (Request $request) {
        $user = $request->user();

        if (!$user) {
            return err(401);
        }

        $name = $request->json('name');
        $description = $request->json('description');
        $public = $request->json('public');

        if (!$name || strlen($name) < 1) {
            return err(400, ['field' => 'name']);
        } else if ($public !== true && $public !== false) {
            return err(400, ['field' => 'name']);
        }

        $playlist_id = Playlist::create([
            'creator_id' => $user->id,
            'name' => $name,
            'description' => $description,
            'public' => $public
        ])->getKey();

        $playlist = Playlist::find($playlist_id);

        return ok($playlist);
    })
        ->name('api.playlist.new'),

    Route::get('/playlist/{uuid}', function (Request $request, string $uuid) {
        $playlist = Playlist::find($uuid);

        if (!$playlist) {
            return err(404);
        }

        $songs = $playlist->songs->song;
        Log::info($songs);

        return ok($playlist);
    })
        ->whereUuid('uuid')
        ->name('api.playlist.by-uuid'),

    Route::post('/playlist/{uuid}/modify', function (Request $request, string $uuid) {
        $name = $request->json('name');
        $description = $request->json('description');
        $public = $request->json('public');

        $playlist = Playlist::find($uuid);

        if (!$playlist) {
            return err(404);
        }

        $user = $request->user();

        if (!$user || $user->id !== $playlist->creator_id) {
            return err(403);
        }

        if ($name !== null && strlen($name) > 0) {
            $playlist->name = $name;
        }

        if ($description && strlen($description) === 0) {
            $playlist->description = null;
        } else if ($description) {
            $playlist->description = $description;
        }

        if ($public !== null) {
            $playlist->public = $public;
        }

        $playlist->save();

        return ok($playlist);
    })
        ->whereUuid('uuid')
        ->name('api.playlist.modify'),

    Route::post('/playlist/{uuid}/delete', function (Request $request, string $uuid) {
        $playlist = Playlist::find($uuid);

        if (!$playlist) {
            return err(404);
        }

        $user = $request->user();

        if (!$user) {
            return err(401);
        } else if ($user->id !== $playlist->creator_id) {
            return err(403);
        }

        $playlist->delete();

        return ok('done');
    })
        ->whereUuid('uuid')
        ->name('api.playlist.delete'),



    Route::get('/user', function (Request $request) {
        return ok($request->user());
    })
        ->name('api.user.me'),

    Route::get('/user/@{username}', function (Request $request, string $username): JsonResponse {
        $user = User::whereUsername($username)->first();

        if (!$user) {
            return err(404);
        }

        return ok($user);
    })
        ->where('username', '/[a-zA-Z0-9_]{4,20}/')
        ->name('api.user.by-username'),

    Route::get('/user/{id}', function (Request $request, string $id): JsonResponse {
        $user = User::find($id);

        if (!$user) {
            return err(404);
        }

        return ok($user);
    })
        ->whereNumber('id')
        ->name('api.user.by-id')
]);
