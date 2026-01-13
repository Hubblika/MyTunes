<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $username = $request->query('username');

        if (!$username) {
            // Only admins can get all playlists from all users
            if ($request->user()?->is_admin) {
                $playlists = Playlist::all();
                return ok($playlists);
            }

            return err(404, ['field' => 'username']);
        }

        $target = User::whereUsername($username)->first();

        if (!$target) {
            return err(404, ['field' => 'username']);
        }

        $playlists = Playlist::whereUserId($target->getKey())
            ->withCount('songs')
            ->get();

        return ok($playlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return err(401);
        }

        $name = $request->json('name');
        $description = $request->json('description');
        $public = $request->json('public', true);

        if (!$name || \strlen($name) < 1) {
            return err(400, ['field' => 'name']);
        }

        $playlist_id = Playlist::create([
            'user_id' => $user->id,
            'name' => $name,
            'description' => $description,
            'public' => $public
        ])->getKey();

        $playlist = Playlist::find($playlist_id);

        return ok($playlist);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $uuid)
    {
        $playlist = Playlist::find($uuid);

        if (!$playlist) {
            return err(404);
        }

        if (!$playlist->public && $playlist->user_id !== $request->user()?->getKey()) {
            return err(404);
        }

        $songs = $playlist->songs->song;

        return ok(['playlist' => $playlist, 'songs' => $songs]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $name = $request->json('name');
        $description = $request->json('description');
        $public = $request->json('public');

        $playlist = Playlist::find($uuid);

        if (!$playlist) {
            return err(404);
        }

        $user = $request->user();

        if (!$user || $user->getKey() !== $playlist->creator_id) {
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $uuid)
    {
        $playlist = Playlist::find($uuid);

        if (!$playlist) {
            return err(404);
        }

        $user = $request->user();

        if (!$user) {
            return err(401);
        } else if ($playlist->user->username !== $user->username) { // <- this is stupid but i think the userid type conversion is messing it up
            return err(403);
        }

        $playlist->delete();

        return response()->noContent();
    }
}
