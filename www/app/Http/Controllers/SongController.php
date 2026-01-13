<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Date;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Storage;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query('q');
        
        if (!$query) {
            $songs = Song::all();
            return ok($songs);
        }

        $cursor = $request->query('cursor');

        $songs = Song::when($query, function ($q) use ($query) {
            $q
                ->where('title', 'like', '%'.$query.'%')
                ->orWhere('genre', 'like', '%'.$query.'%');
                // TODO: allow search to find song by artist name?
                // ->orWhereHas('user', function ($q) use ($query) {
                //     $q
                //         ->where('is_searchable', true)
                //         ->where('username', 'like', '%'.$query.'%');
                // });
        })
            ->orderBy('updated_at')
            ->cursorPaginate(LIMIT, ['*'], 'cursor', $cursor);

        return response()->json([
            'data' => $songs->items(),
            'next_cursor' => optional($songs->nextCursor())->encode()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $json = $request->acceptsJson();

        if (!$user) {
            return $json ? err(401) : Inertia::render('Admin', ['error' => 'Unauthenticated']);
        } else if (!$user->is_admin) {
            return $json ? err(403) : Inertia::render('Admin', ['error' => 'Forbidden']);
        }

        $audio = $request->file('audio');
        $cover = $request->file('cover');

        $title = $request->input('title');
        $date = $request->input('date');
        $duration = $request->input('duration');
        $explicit = $request->input('is_explicit', false);

        if (!$audio) {
            return $json ? err(400, ['field' => 'audio']) : Inertia::render('Admin', ['error' => 'audio']);
        } else if (!$cover) {
            return $json ? err(400, ['field' => 'cover']) : Inertia::render('Admin', ['error' => 'cover']);
        } else if (!$title) {
            return $json ? err(400, ['field' => 'title']) : Inertia::render('Admin', ['error' => 'title']);
        } else if (!$date) {
            return $json ? err(400, ['field' => 'date']) : Inertia::render('Admin', ['error' => 'date']);
        } else if (!$duration) {
            return $json ? err(400, ['field' => 'duration']) : Inertia::render('Admin', ['error' => 'duration']);
        }

        $uuid = uuid_create();

        Storage::disk('public')->put('songs/'.$uuid.'/audio.mp3', file_get_contents($audio));
        Storage::disk('public')->put('songs/'.$uuid.'/cover.jpg', file_get_contents($cover));

        $uuid = Song::create([
            'title' => $title,
            'artist' => 'todo',
            'url' => '/storage/songs/'.$uuid.'/audio.mp3',
            'cover_url' => '/storage/songs/'.$uuid.'/cover.jpg',
            'date' => Date::parse($date),
            'duration' => $duration,
            'is_explicit' => $explicit
        ])->getKey();

        $song = Song::find($uuid);
        return $json ? ok($song) : Inertia::render('Admin');
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
