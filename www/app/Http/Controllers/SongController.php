<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    /**
     * Display all songs
     */
    public function index(Request $request)
    {
        $query = $request->input('q');

        $songsQuery = Song::query();

        if ($query) {
            $query = strtolower($query);
            $songsQuery->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$query}%"])
                    ->orWhereRaw('LOWER(artist) LIKE ?', ["%{$query}%"])
                    ->orWhereRaw('LOWER(album) LIKE ?', ["%{$query}%"]);
            });
        }

        $songs = $songsQuery->orderBy('updated_at')->get();

        return response()->json([
            'data' => $songs->map(fn($song) => $this->transformSong($song)),
        ]);
    }

    /**
     * Store a newly created song
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) return redirect()->back()->with('error', 'Unauthorized');
        if (!$user->is_admin) return redirect()->back()->with('error', 'Forbidden');

        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'album' => 'required|string|max:255',
            'audio' => 'required|file|mimes:mp3',
            'cover' => 'nullable|file|image',
            'date' => 'required|date',
            'duration' => 'required|integer',
            'genre' => 'nullable|string|max:60',
        ]);

        $song = new Song();
        $song->title = $request->input('title');
        $song->artist = $request->input('artist');
        $song->album = $request->input('album');
        $song->date = $request->input('date');
        $song->duration = $request->input('duration');
        $song->genre = $request->input('genre');

        $song->save();

        if ($audioFile = $request->file('audio')) {
            $fileName = "{$song->title}.mp3";
            $audioFile->storeAs('songs', $fileName, 'local');
            $song->file_name = $fileName;
        }


        if ($coverFile = $request->file('cover')) {
            $coverFileName = "{$song->title}.jpg";
            $coverPath = $coverFile->storeAs('songCovers', $coverFileName, 'public');
            $song->cover_url = "/storage/{$coverPath}";
        } else {
            $song->cover_url = '/storage/default-cover.png';
        }

        $song->save();

        return redirect()->back()->with('success', 'Song uploaded successfully!');
    }




    /**
     * Show a single song
     */
    public function show(string $uuid)
    {
        $song = Song::find($uuid);
        if (!$song) {
            return response()->json(['error' => 'Song not found'], 404);
        }

        return response()->json($this->transformSong($song));
    }

    /**
     * Delete a song
     */
    public function destroy(Request $request, string $uuid)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);
        if (!$user->is_admin) return response()->json(['error' => 'Forbidden'], 403);

        $song = Song::find($uuid);
        if (!$song) return response()->json(['error' => 'Song not found'], 404);

        Storage::disk('public')->delete($song->file_name);
        if ($song->cover_url && !str_contains($song->cover_url, 'default-cover.png')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $song->cover_url));
        }

        $song->delete();

        return response()->noContent();
    }

    /**
     * Transform song model into API-ready array
     */
    protected function transformSong(Song $song): array
    {
        return [
            'uuid' => $song->uuid,
            'title' => $song->title,
            'artist' => $song->artist,
            'album' => $song->album,
            'url' => route('songs.stream', $song->uuid),
            'cover_url' => $song->cover_url ?? '/storage/app/public/covers/default_cover.png',
            'date' => $song->date,
            'duration' => $song->duration,
            'genre' => $song->genre,
            'created_at' => $song->created_at?->toDateTimeString(),
            'updated_at' => $song->updated_at?->toDateTimeString(),
        ];
    }
}
