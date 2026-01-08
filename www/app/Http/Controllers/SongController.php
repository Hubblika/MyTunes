<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Date;
use Illuminate\Http\Request;
use Storage;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $songs = Song::all();
        return ok($songs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return err(401);
        } else if (!$user->is_admin) {
            return err(403);
        }

        $audio = $request->file('audio.mp3');
        $cover = $request->file('cover.png');

        $title = $request->input('title');
        $created_at = $request->input('created_at');
        $duration = $request->input('duration');
        $explicit = $request->input('is_explicit');

        if (!$audio) {
            return err(400, ['field' => 'audio']);
        } else if (!$audio) {
            return err(400, ['field' => 'cover']);
        } else if (!$title) {
            return err(400, ['field' => 'title']);
        } else if (!$duration) {
            return err(400, ['field' => 'duration']);
        }

        $uuid = uuid_create();

        Storage::disk('local')->put('uploads/songs/{$uuid}/audio.mp3', file_get_contents($audio));
        Storage::disk('local')->put('uploads/songs/{$uuid}/cover.png', file_get_contents($cover));

        $uuid = Song::create([
            'title' => $title,
            'created_at' => Date::parse($created_at) || Date::now(),
            'duration' => $duration,
            'is_explicit' => $explicit || false
        ])->getKey();

        $song = Song::find($uuid);
        return ok($song);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $song = Song::find($uuid);

        if (!$song) {
            return err(404);
        }

        return ok($song);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        // TODO
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $uuid)
    {
        $user = $request->user();

        if (!$user) {
            return err(401);
        } else if (!$user->is_admin) {
            return err(403);
        }

        return response()->noContent();
    }
}
