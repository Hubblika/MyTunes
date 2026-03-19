<?php

use App\Models\User;
use function Pest\Laravel\{actingAs, getJson, deleteJson};

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true, 'email' => 'admin@example.com']);
    $this->user = User::factory()->create(['username' => 'johndoe']);
});

test('index returns all users except the main admin email', function () {
    User::factory()->count(2)->create();

    // The routes file shows this is protected by 'can:admin'
    $response = actingAs($this->admin)->getJson('/users');

    $response->assertStatus(200);
    
    $data = $response->json();
    // Should see the 1 regular user + 2 new ones, but NOT the admin@example.com
    foreach ($data as $user) {
        expect($user['email'])->not->toBe('admin@example.com');
    }
});

test('can show a user by their username', function () {
    $response = actingAs($this->user)->getJson("/users/johndoe");

    $response->assertStatus(200);
    
    // Check if the username matches in the response
    // Using json() directly to handle your ok() helper wrapping
    $data = $response->json();
    $username = $data['username'] ?? $data['data']['username'] ?? null;
    
    expect($username)->toBe('johndoe');
});

test('returns 404 for non-existent username', function () {
    $response = actingAs($this->user)->getJson("/users/ghost_user");

    $response->assertStatus(404);
});

test('user can delete their own account', function () {
    $response = actingAs($this->user)->deleteJson("/users/{$this->user->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
});

test('user cannot delete another users account', function () {
    $otherUser = User::factory()->create();

    $response = actingAs($this->user)->deleteJson("/users/{$otherUser->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
});