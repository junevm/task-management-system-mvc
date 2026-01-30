<?php

declare(strict_types=1);

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their tasks', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('tasks.index'));

    $response->assertStatus(200)
        ->assertViewHas('tasks');
});

test('unauthenticated user cannot view tasks', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect(route('login'));
});

test('user can create a task', function () {
    $user = User::factory()->create();

    $taskData = [
        'title' => 'New Task',
        'description' => 'Task Description',
        'status' => TaskStatus::PENDING->value,
        'priority' => TaskPriority::HIGH->value,
        'due_date' => '2026-12-31',
    ];

    $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'user_id' => $user->id,
    ]);
});

test('task creation requires title', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'description' => 'Task Description',
        'status' => TaskStatus::PENDING->value,
        'priority' => TaskPriority::MEDIUM->value,
    ]);

    $response->assertSessionHasErrors('title');
});

test('user can view their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('tasks.show', $task));

    $response->assertStatus(200)
        ->assertViewHas('task', $task);
});

test('user cannot view another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $response = $this->actingAs($user1)->get(route('tasks.show', $task));

    $response->assertStatus(403);
});

test('user can update their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => TaskStatus::COMPLETED->value,
        'priority' => TaskPriority::LOW->value,
        'due_date' => '2026-12-31',
    ];

    $response = $this->actingAs($user)->patch(route('tasks.update', $task), $updateData);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'status' => TaskStatus::COMPLETED->value,
    ]);
});

test('user cannot update another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $response = $this->actingAs($user1)->patch(route('tasks.update', $task), [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => TaskStatus::COMPLETED->value,
        'priority' => TaskPriority::LOW->value,
    ]);

    $response->assertStatus(403);
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

    $response->assertRedirect(route('tasks.index'));
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('user cannot delete another users task', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);

    $response = $this->actingAs($user1)->delete(route('tasks.destroy', $task));

    $response->assertStatus(403);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});
