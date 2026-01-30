<?php

declare(strict_types=1);

use App\Actions\Tasks\CreateTaskAction;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a task with all fields', function () {
    $user = User::factory()->create();
    $action = new CreateTaskAction();

    $task = $action->execute(
        user: $user,
        title: 'Test Task',
        description: 'Test Description',
        status: TaskStatus::PENDING,
        priority: TaskPriority::HIGH,
        dueDate: '2026-12-31'
    );

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->user_id)->toBe($user->id)
        ->and($task->title)->toBe('Test Task')
        ->and($task->description)->toBe('Test Description')
        ->and($task->status)->toBe(TaskStatus::PENDING)
        ->and($task->priority)->toBe(TaskPriority::HIGH)
        ->and($task->due_date->format('Y-m-d'))->toBe('2026-12-31');
});

test('it creates a task with minimal fields', function () {
    $user = User::factory()->create();
    $action = new CreateTaskAction();

    $task = $action->execute(
        user: $user,
        title: 'Minimal Task',
        description: null,
        status: TaskStatus::PENDING,
        priority: TaskPriority::MEDIUM,
        dueDate: null
    );

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->title)->toBe('Minimal Task')
        ->and($task->description)->toBeNull()
        ->and($task->due_date)->toBeNull();
});

test('it associates task with the correct user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $action = new CreateTaskAction();

    $task = $action->execute(
        user: $user1,
        title: 'User 1 Task',
        description: null,
        status: TaskStatus::PENDING,
        priority: TaskPriority::MEDIUM,
        dueDate: null
    );

    expect($task->user_id)->toBe($user1->id)
        ->and($task->user_id)->not->toBe($user2->id);
});
