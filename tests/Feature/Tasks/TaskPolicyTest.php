<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can only view own tasks policy', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $task1 = Task::factory()->create(['user_id' => $user1->id]);
    $task2 = Task::factory()->create(['user_id' => $user2->id]);

    expect($user1->can('view', $task1))->toBeTrue()
        ->and($user1->can('view', $task2))->toBeFalse();
});

test('user can only update own tasks policy', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $task1 = Task::factory()->create(['user_id' => $user1->id]);
    $task2 = Task::factory()->create(['user_id' => $user2->id]);

    expect($user1->can('update', $task1))->toBeTrue()
        ->and($user1->can('update', $task2))->toBeFalse();
});

test('user can only delete own tasks policy', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $task1 = Task::factory()->create(['user_id' => $user1->id]);
    $task2 = Task::factory()->create(['user_id' => $user2->id]);

    expect($user1->can('delete', $task1))->toBeTrue()
        ->and($user1->can('delete', $task2))->toBeFalse();
});
