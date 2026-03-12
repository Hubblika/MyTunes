<?php

use App\Models\User;
use App\Models\Song;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{actingAs};

beforeEach(function () {
    Storage::fake('local');
    $this->user = User::factory()->create();
    
    $this->song = Song::factory()->create([
        'file_name' => 'test-stream.mp3'
    ]);

    // Match the path used in your StreamController: songs/filename
    Storage::disk('local')->put("songs/{$this->song->file_name}", str_repeat('a', 1000));
});

test('it streams the full song when no range is provided', function () {
    // FIX: Changed URL to /stream/{song} to match your routes/web.php
    $response = actingAs($this->user)
        ->withHeaders(['Accept' => 'application/json'])
        ->get("/stream/{$this->song->uuid}");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'audio/mpeg');
    $response->assertHeader('Content-Length', 1000);
    
    expect($response->streamedContent())->toBe(str_repeat('a', 1000));
});

test('it handles byte range requests for scrubbing', function () {
    // FIX: Changed URL to /stream/{song}
    $response = actingAs($this->user)
        ->withHeaders([
            'Accept' => 'application/json',
            'Range' => 'bytes=0-499'
        ])
        ->get("/stream/{$this->song->uuid}");

    $response->assertStatus(206);
    $response->assertHeader('Content-Range', 'bytes 0-499/1000');
    $response->assertHeader('Content-Length', 500);
    
    expect(strlen($response->streamedContent()))->toBe(500);
});

test('it returns 404 if the song record does not exist', function () {
    $fakeUuid = (string) str()->uuid();
    $response = actingAs($this->user)
        ->withHeaders(['Accept' => 'application/json'])
        ->get("/stream/{$fakeUuid}");

    $response->assertStatus(404);
});

test('it returns 404 if the file is missing from the disk', function () {
    $brokenSong = Song::factory()->create(['file_name' => 'missing.mp3']);
    // Ensure the record exists but the file does not
    Storage::disk('local')->delete("songs/missing.mp3");

    $response = actingAs($this->user)
        ->withHeaders(['Accept' => 'application/json'])
        ->get("/stream/{$brokenSong->uuid}");

    $response->assertStatus(404);
});