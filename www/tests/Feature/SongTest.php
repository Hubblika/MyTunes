<?php

use App\Models\User;
use App\Models\Song;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{actingAs, getJson, post, deleteJson};

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

test('can search for songs by title, artist, or album', function () {
    Song::factory()->create(['title' => 'Lose Yourself', 'artist' => 'Eminem', 'album' => '8 Mile']);
    Song::factory()->create(['title' => 'Sailing', 'artist' => 'Christopher Cross', 'album' => 'Self']);

    // Log in so we don't get a 401
    $response = actingAs($this->user)->getJson('/songs?q=Eminem');
    
    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.title'))->toBe('Lose Yourself');

    $response = actingAs($this->user)->getJson('/songs?q=sail');
    expect($response->json('data'))->toHaveCount(1);
});

test('admin can upload a song', function () {
    // Use create() instead of image() to avoid GD library requirement
    $audio = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');
    $cover = UploadedFile::fake()->create('cover.jpg', 500, 'image/jpeg');

    $response = actingAs($this->admin)->post('/songs', [
        'title' => 'Test Song',
        'artist' => 'Test Artist',
        'album' => 'Test Album',
        'audio' => $audio,
        'cover' => $cover,
        'date' => '2024-01-01',
        'duration' => 180,
        'genre' => 'Pop'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Song uploaded successfully!');

    $this->assertDatabaseHas('songs', ['title' => 'Test Song']);

    Storage::disk('local')->assertExists('songs/Test Song.mp3');
    Storage::disk('public')->assertExists('songCovers/Test Song.jpg');
});

test('can show a single song via json', function () {
    $song = Song::factory()->create();

    // Log in so we don't get a 401
    $response = actingAs($this->user)->getJson("/songs/{$song->uuid}");

    $response->assertStatus(200)
             ->assertJsonPath('uuid', $song->uuid);
});

test('non-admin cannot upload a song', function () {
    $response = actingAs($this->user)->post('/songs', [
        'title' => 'Hacker Song'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Forbidden');
});

test('admin can delete a song and its files', function () {
    $song = Song::factory()->create([
        'file_name' => 'delete-me.mp3',
        'cover_url' => '/storage/songCovers/delete-me.jpg'
    ]);

    // Seed fake files
    Storage::disk('public')->put('delete-me.mp3', 'contents');
    Storage::disk('public')->put('songCovers/delete-me.jpg', 'contents');

    $response = actingAs($this->admin)->deleteJson("/songs/{$song->uuid}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('songs', ['uuid' => $song->uuid]);
});