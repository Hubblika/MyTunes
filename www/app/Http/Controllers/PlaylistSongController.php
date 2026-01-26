<?php

namespace App\Http\Controllers;

use App\Models\PlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaylistSongController extends Controller
{
    public function index($playlist)
    {
        $songs = PlaylistSong::where('playlist_id', $playlist)->get();
        return response()->json($songs);
    }

    public function store(Request $request, $playlist)
    {
        $request->validate([
            'song_id' => 'required|uuid|exists:songs,uuid',
            'position' => 'nullable|integer',
        ]);

        $playlistSong = PlaylistSong::create([
            'playlist_id' => $playlist,
            'song_id' => $request->song_id,
            'position' => $request->position,
        ]);

        return response()->json($playlistSong, Response::HTTP_CREATED);
    }

    public function destroy($playlist, $song)
    {
        $deleted = PlaylistSong::where('playlist_id', $playlist)
            ->where('song_id', $song)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Song removed from playlist']);
        }

        return response()->json(['message' => 'Song not found in playlist'], Response::HTTP_NOT_FOUND);
    }
}
