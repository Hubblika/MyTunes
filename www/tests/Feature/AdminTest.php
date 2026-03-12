<?php

use App\Models\User;
use function Pest\Laravel\{actingAs, putJson, assertDatabaseHas};

test('admin can update multiple users admin status', function () {
    // 1. Arrange: Create the admin and the target users
    $admin = User::factory()->create(['is_admin' => true]);
    $userA = User::factory()->create(['is_admin' => false]);
    $userB = User::factory()->create(['is_admin' => true]);

    // 2. Act: Hit the route
    $response = actingAs($admin)->putJson('/admin', [
        'users' => [
            ['id' => $userA->id, 'is_admin' => true],
            ['id' => $userB->id, 'is_admin' => false],
        ]
    ]);

    // 3. Assert: Success and DB check
    $response->assertStatus(200)
             ->assertJson(['message' => 'Admin status updated.']);

    // Verify the data changed in the database
    expect($userA->fresh()->is_admin)->toBeTrue();
    expect($userB->fresh()->is_admin)->toBeFalse();
});

test('non admins cannot update admin status', function () {
    // Arrange: Create a regular user
    $regularUser = User::factory()->create(['is_admin' => false]);
    $targetUser = User::factory()->create(['is_admin' => false]);

    // Act: Try to perform admin actions
    $response = actingAs($regularUser)->putJson('/admin', [
        'users' => [
            ['id' => $targetUser->id, 'is_admin' => true],
        ]
    ]);

    // Assert: Forbidden
    $response->assertStatus(403);
    expect($targetUser->fresh()->is_admin)->toBeFalse();
});

test('update fails with invalid data', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    // Act: Send an empty users array
    $response = actingAs($admin)->putJson('/admin', [
        'users' => []
    ]);

    // Assert: Validation error
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['users']);
});

test('update fails if user id is invalid', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    // Act: Send a non-existent ID
    $response = actingAs($admin)->putJson('/admin', [
        'users' => [
            ['id' => 99999, 'is_admin' => true],
        ]
    ]);

    // Assert: Validation error on the specific nested ID
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['users.0.id']);
});