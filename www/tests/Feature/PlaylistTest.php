<?php

use App\Models\User;
use App\Models\Playlist;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{actingAs, getJson, postJson, putJson, deleteJson};

beforeEach(function () {
    Storage::fake('public'); // Intercept file uploads
    $this->user = User::factory()->create(['username' => 'testuser']);
});

test('user can create a playlist', function () {
    $response = actingAs($this->user)->postJson('/playlists', [
        'name' => 'My Summer Hits',
        'description' => 'Best songs for summer',
        'public' => true
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('playlists', [
        'name' => 'My Summer Hits',
        'user_id' => $this->user->id
    ]);
});

test('user can update their own playlist', function () {
    $playlist = Playlist::factory()->create(['user_id' => $this->user->id]);
    
    // We use create() instead of image() to avoid needing the GD library
    $file = UploadedFile::fake()->create('cover.jpg', 500); 

    $response = actingAs($this->user)->putJson("/playlists/{$playlist->uuid}", [
        'name' => 'Updated Name',
        'cover' => $file
    ]);

    $response->assertStatus(200);
    expect($playlist->fresh()->name)->toBe('Updated Name');
    
    // Check if the file was actually "stored"
    $playlist->refresh();
    $path = str_replace('/storage/', '', $playlist->cover_url);
    Storage::disk('public')->assertExists($path);
});

test('user cannot update someone elses playlist', function () {
    $otherUser = User::factory()->create();
    $playlist = Playlist::factory()->create(['user_id' => $otherUser->id]);

    $response = actingAs($this->user)->putJson("/playlists/{$playlist->uuid}", [
        'name' => 'Hacked Name'
    ]);

    $response->assertStatus(403);
});

test('user can delete their own playlist', function () {
    $playlist = Playlist::factory()->create(['user_id' => $this->user->id]);

    $response = actingAs($this->user)->deleteJson("/playlists/{$playlist->uuid}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('playlists', ['uuid' => $playlist->uuid]);
});

test('users must provide a username to see playlists', function () {
    $response = actingAs($this->user)->getJson('/playlists');

    // Based on your controller: return err(404, ['field' => 'username']);
    $response->assertStatus(404);
});