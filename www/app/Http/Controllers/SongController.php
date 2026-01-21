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

        if (!$user) {
            return err(401);
        } else if (!$user->is_admin) {
            return err(403);
        }

        $audio = $request->file('audio');
        // $cover = $request->file('cover');

        $title = $request->input('title');
        $date = $request->input('date');
        $duration = $request->input('duration');
        $genre = $request->input('genre');
        $explicit = $request->input('is_explicit', false);

        if (!$request->hasFile('audio') || !$audio) {
            return err(400, ['field' => 'audio']);
        } else if (!$title) {
            return err(400, ['field' => 'title']);
        } else if (!$date) {
            return err(400, ['field' => 'date']);
        } else if (!$duration) {
            return err(400, ['field' => 'duration']);
        }

        $uuid = Song::create([
            'title' => $title,
            'artist' => 'todo',
            
            // if uuid is generated automatically in `Song::boot` instead of here with `uuid_create()`, `$uuid` value isn't available to use here yet
            'url' => /* '/storage/songs/'.$uuid.'/audio.mp3' */ '?',
            'cover_url' => /* '/storage/songs/'.$uuid.'/cover.jpg' */ '?',

            'date' => Date::parse($date),
            'duration' => $duration,
            'genre' => $genre,
            // 'is_explicit' => $explicit
        ])->getKey();

        Storage::disk('public')->put('songs/'.$uuid.'.mp3', file_get_contents($audio));
        // Storage::disk('public')->put('songs/'.$uuid.'/cover.png', file_get_contents($cover));

        $song = Song::find($uuid);
        return $request->acceptsJson() ? ok($song) : Inertia::render('Admin');
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
