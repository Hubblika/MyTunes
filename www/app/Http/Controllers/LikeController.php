<?php

namespace App\Http\Controllers;
use App\Models\UserLike;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $uuid)
    {
        $user = Auth::user();

        $song = Song::where('id', $uuid)->firstOrFail();

        UserLike::firstOrCreate([
            'user_id' => $user->id,
            'song_id' => $song->id,
        ]);

        return response()->json([
            'message' => 'Song liked'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
