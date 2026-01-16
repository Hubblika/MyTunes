<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLike;
use App\Models\Song;

class LikeController extends Controller
{
    public function store(Request $request, string $uuid)
    {
        $user = $request->user();

        $song = Song::where('uuid', $uuid)->firstOrFail();

        UserLike::firstOrCreate([
            'user_id' => $user->id,
            'song_id' => $song->uuid,
        ]);

        return response()->json([
            'message' => 'Song liked'
        ]);
    }

    public function show(Request $request, $uuid)
    {
        $user = $request->user();

        $liked = UserLike::where('user_id', $user->id)
            ->where('song_id', $uuid)
            ->exists();

        return response()->json(['liked' => $liked]);
    }




    public function destroy(Request $request, string $uuid)
    {
        $user = $request->user();

        $song = Song::where('uuid', $uuid)->firstOrFail();

        UserLike::where('user_id', $user->id)
            ->where('song_id', $song->uuid)
            ->delete();

        return response()->json([
            'message' => 'Song unliked'
        ]);
    }
}
