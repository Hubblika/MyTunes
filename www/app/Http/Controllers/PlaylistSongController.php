<?php

namespace App\Http\Controllers;

use App\Models\PlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Playlist;

class PlaylistSongController extends Controller
{
    public function index($playlist)
    {
        $playlist = Playlist::where('uuid', $playlist)->firstOrFail();

        $songs = $playlist->songs()
            ->orderBy('playlist_songs.created_at', 'desc')
            ->get();

        return response()->json($songs);
    }

    public function store(Request $request, $playlist)
    {
        $request->validate([
            'song_id' => 'required|uuid|exists:songs,uuid',
            'position' => 'nullable|integer',
        ]);

        $playlistModel = Playlist::where('uuid', $playlist)->firstOrFail();

        $playlistModel->songs()->attach($request->song_id, [
            'position' => $request->position,
        ]);

        return response()->json(['message' => 'Song added'], Response::HTTP_CREATED);
    }

    public function destroy($playlist, $song)
    {
        $playlistModel = Playlist::where('uuid', $playlist)->firstOrFail();

        $detached = $playlistModel->songs()->detach($song);

        if ($detached) {
            return response()->json(['message' => 'Song removed from playlist']);
        }

        return response()->json(['message' => 'Song not found in playlist'], Response::HTTP_NOT_FOUND);
    }
}
