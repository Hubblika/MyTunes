<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $username = $request->query('username');

        // Admins can get all playlists
        if (!$username) {
            $user = $request->user();
            if ($user && $user->is_admin) {
                $playlists = Playlist::withCount('songs')->get();
                return ok($playlists);
            }
            return err(404, ['field' => 'username']);
        }

        $target = User::whereUsername($username)->first();
        if (!$target) {
            return err(404, ['field' => 'username']);
        }

        $playlists = Playlist::whereUserId($target->id)
            ->withCount('songs')
            ->get();

        return ok($playlists);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $name = $request->input('name');
        $description = $request->input('description');
        $public = $request->input('public', true);

        if (!$name || strlen($name) < 1) {
            return err(400, ['field' => 'name']);
        }

        $playlist = Playlist::create([
            'user_id' => $user->id,
            'name' => $name,
            'description' => $description,
            'public' => (bool) $public,
        ]);

        return ok($playlist->loadCount('songs'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $uuid)
    {
        $playlist = Playlist::where('uuid', $uuid)
            ->withCount('songs')
            ->first();

        if (!$playlist) return err(404);

        $user = $request->user();
        if (!$playlist->public && $playlist->user_id !== $user->id) {
            return err(404);
        }

        return ok($playlist);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $uuid)
    {
        error_log("Method: " . $request->method());
    error_log("All Data: " . print_r($request->all(), true));
    error_log("Files: " . print_r($request->allFiles(), true));
        $playlist = Playlist::where('uuid', $uuid)->first();
        if (!$playlist) return err(404);

        $user = $request->user();
        if ($playlist->user_id !== $user->id) return err(403);

        $request->validate([
            'name' => 'nullable|string|min:1',
            'cover' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'public' => 'boolean',
        ]);

        if ($request->filled('name')) {
            $playlist->name = $request->input('name');
        }

        if ($request->has('description')) {
            $desc = $request->input('description');
            $playlist->description = $desc === '' ? null : $desc;
        }

        if ($request->has('public')) {
            $playlist->public = (bool) $request->input('public');
        }

        if ($request->hasFile('cover')) {
            if ($playlist->cover_url) {
                $oldPath = str_replace('/storage/', '', $playlist->cover_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('cover')->store('covers', 'public');
            $playlist->cover_url = Storage::url($path);
        }

        $playlist->save();

        return ok($playlist->loadCount('songs'));
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Request $request, string $uuid)
    {
        $playlist = Playlist::where('uuid', $uuid)->first();
        if (!$playlist) return err(404);

        $user = $request->user();
        if ($playlist->user_id !== $user->id) {
            return err(403);
        }

        if ($playlist->cover_url) {
            $oldPath = str_replace('/storage/', '', $playlist->cover_url);
            Storage::disk('public')->delete($oldPath);
        }

        $playlist->delete();

        return response()->noContent();
    }
}
