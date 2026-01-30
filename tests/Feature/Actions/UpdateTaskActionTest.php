<?php

declare(strict_types=1);

use App\Actions\Tasks\UpdateTaskAction;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it updates all task fields', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original Title',
        'status' => TaskStatus::PENDING,
        'priority' => TaskPriority::LOW,
    ]);

    $action = new UpdateTaskAction();

    $updatedTask = $action->execute(
        task: $task,
        title: 'Updated Title',
        description: 'Updated Description',
        status: TaskStatus::IN_PROGRESS,
        priority: TaskPriority::HIGH,
        dueDate: '2026-12-31'
    );

    expect($updatedTask->title)->toBe('Updated Title')
        ->and($updatedTask->description)->toBe('Updated Description')
        ->and($updatedTask->status)->toBe(TaskStatus::IN_PROGRESS)
        ->and($updatedTask->priority)->toBe(TaskPriority::HIGH)
        ->and($updatedTask->due_date->format('Y-m-d'))->toBe('2026-12-31');
});

test('it returns fresh task instance', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $action = new UpdateTaskAction();

    $updatedTask = $action->execute(
        task: $task,
        title: 'New Title',
        description: null,
        status: TaskStatus::COMPLETED,
        priority: TaskPriority::MEDIUM,
        dueDate: null
    );

    expect($updatedTask->wasRecentlyCreated)->toBeFalse()
        ->and($updatedTask->exists)->toBeTrue();
});
