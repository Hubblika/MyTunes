<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLike;
use App\Models\Song;

class LikeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $likes = UserLike::where('user_id', $user->id)
            ->pluck('song_id');

        return response()->json([
            'likes' => $likes,
        ]);
    }

    public function store(Request $request, string $uuid)
    {
        $user = $request->user();

        Song::where('uuid', $uuid)->firstOrFail();

        UserLike::firstOrCreate([
            'user_id' => $user->id,
            'song_id' => $uuid,
        ]);

        return response()->json([
            'message' => 'Song liked',
        ], 201);
    }

    public function show(Request $request, string $uuid)
    {
        $user = $request->user();

        Song::where('uuid', $uuid)->firstOrFail();

        $liked = UserLike::where('user_id', $user->id)
            ->where('song_id', $uuid)
            ->exists();

        return response()->json([
            'liked' => $liked,
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $user = $request->user();

        Song::where('uuid', $uuid)->firstOrFail();

        UserLike::where('user_id', $user->id)
            ->where('song_id', $uuid)
            ->delete();

        return response()->json([
            'message' => 'Song unliked',
        ]);
    }
}
