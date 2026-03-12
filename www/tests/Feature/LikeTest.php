<?php

use App\Models\User;
use App\Models\Song;
use App\Models\UserLike;
use function Pest\Laravel\{actingAs, getJson, postJson, deleteJson};

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->song = Song::factory()->create(['uuid' => (string) str()->uuid()]);
});

test('user can like a song', function () {
    $response = actingAs($this->user)
        ->postJson("/likes/{$this->song->uuid}");

    $response->assertStatus(201)
             ->assertJson(['message' => 'Song liked']);

    $this->assertDatabaseHas('user_likes', [
        'user_id' => $this->user->id,
        'song_id' => $this->song->uuid,
    ]);
});

test('user cannot like a non-existent song', function () {
    $response = actingAs($this->user)
        ->postJson("/likes/invalid-uuid");

    $response->assertStatus(404);
});

test('user can check if they liked a song', function () {
    UserLike::create([
        'user_id' => $this->user->id,
        'song_id' => $this->song->uuid
    ]);

    $response = actingAs($this->user)
        ->getJson("/likes/{$this->song->uuid}");

    $response->assertStatus(200)
             ->assertJson(['liked' => true]);
});

test('user can see their list of liked songs', function () {
    UserLike::create([
        'user_id' => $this->user->id,
        'song_id' => $this->song->uuid
    ]);

    $response = actingAs($this->user)
        ->getJson('/likes');

    $response->assertStatus(200)
             ->assertJsonCount(1, 'likes')
             ->assertJsonPath('likes.0.uuid', $this->song->uuid);
});

test('user can unlike a song', function () {
    UserLike::create([
        'user_id' => $this->user->id,
        'song_id' => $this->song->uuid
    ]);

    $response = actingAs($this->user)
        ->deleteJson("/likes/{$this->song->uuid}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Song unliked']);

    $this->assertDatabaseMissing('user_likes', [
        'user_id' => $this->user->id,
        'song_id' => $this->song->uuid,
    ]);
});

test('unauthenticated users cannot interact with likes', function () {
    getJson('/likes')->assertStatus(401);
    postJson("/likes/{$this->song->uuid}")->assertStatus(401);
});