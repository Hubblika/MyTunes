<?php

use App\Models\User;
use App\Models\Song;
use App\Models\Playlist;
use function Pest\Laravel\{actingAs, getJson, postJson, deleteJson};

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->playlist = Playlist::factory()->create(['user_id' => $this->user->id]);
    $this->song = Song::factory()->create();
});

test('can list songs in a playlist', function () {
    // Attach the song to the playlist first
    $this->playlist->songs()->attach($this->song->uuid, ['position' => 1]);

    $response = actingAs($this->user)
        ->getJson("/playlists/{$this->playlist->uuid}/songs");

    $response->assertStatus(200)
             ->assertJsonCount(1);
    
    expect($response->json()[0]['uuid'])->toBe($this->song->uuid);
});

test('returns empty collection for null-uuid playlist', function () {
    $nullUuid = '00000000-0000-0000-0000-000000000000';
    
    $response = actingAs($this->user)
        ->getJson("/playlists/{$nullUuid}/songs");

    $response->assertStatus(200)
             ->assertJsonCount(0);
});

test('user can add a song to a playlist', function () {
    $response = actingAs($this->user)
        ->postJson("/playlists/{$this->playlist->uuid}/songs", [
            'song_id' => $this->song->uuid,
            'position' => 5
        ]);

    $response->assertStatus(201)
             ->assertJson(['message' => 'Song added']);

    $this->assertDatabaseHas('playlist_songs', [
        'playlist_id' => $this->playlist->uuid,
        'song_id' => $this->song->uuid,
        'position' => 5
    ]);
});

test('user can remove a song from a playlist', function () {
    // Attach it first
    $this->playlist->songs()->attach($this->song->uuid);

    $response = actingAs($this->user)
        ->deleteJson("/playlists/{$this->playlist->uuid}/songs/{$this->song->uuid}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Song removed from playlist']);

    $this->assertDatabaseMissing('playlist_songs', [
        'playlist_id' => $this->playlist->uuid,
        'song_id' => $this->song->uuid
    ]);
});

test('returns 404 when removing a song that is not in the playlist', function () {
    $response = actingAs($this->user)
        ->deleteJson("/playlists/{$this->playlist->uuid}/songs/{$this->song->uuid}");

    $response->assertStatus(404)
             ->assertJson(['message' => 'Song not found in playlist']);
});

test('adding non-existent song returns validation error', function () {
    $response = actingAs($this->user)
        ->postJson("/playlists/{$this->playlist->uuid}/songs", [
            'song_id' => (string) str()->uuid(), // random UUID not in DB
            'position' => 1
        ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['song_id']);
});