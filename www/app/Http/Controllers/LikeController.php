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
}
