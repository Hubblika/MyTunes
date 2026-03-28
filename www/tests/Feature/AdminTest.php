<?php

use App\Models\User;
use function Pest\Laravel\{actingAs, putJson, assertDatabaseHas};

test('admin can update multiple users admin status', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $userA = User::factory()->create(['is_admin' => false]);
    $userB = User::factory()->create(['is_admin' => true]);

    $response = actingAs($admin)->putJson('/admin', [
        'users' => [
            ['id' => $userA->id, 'is_admin' => true],
            ['id' => $userB->id, 'is_admin' => false],
        ]
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Admin status updated.']);

    expect($userA->fresh()->is_admin)->toBeTrue();
    expect($userB->fresh()->is_admin)->toBeFalse();
});

test('non admins cannot update admin status', function () {
    $regularUser = User::factory()->create(['is_admin' => false]);
    $targetUser = User::factory()->create(['is_admin' => false]);

    $response = actingAs($regularUser)->putJson('/admin', [
        'users' => [
            ['id' => $targetUser->id, 'is_admin' => true],
        ]
    ]);

    $response->assertStatus(403);
    expect($targetUser->fresh()->is_admin)->toBeFalse();
});

test('update fails with invalid data', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = actingAs($admin)->putJson('/admin', [
        'users' => []
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['users']);
});

test('update fails if user id is invalid', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = actingAs($admin)->putJson('/admin', [
        'users' => [
            ['id' => 99999, 'is_admin' => true],
        ]
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['users.0.id']);
});