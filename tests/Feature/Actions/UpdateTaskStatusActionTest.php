<?php

declare(strict_types=1);

use App\Actions\Tasks\UpdateTaskStatusAction;
use App\Enums\TaskStatus;
use App\Events\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('it updates task status', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => TaskStatus::PENDING,
    ]);

    $action = new UpdateTaskStatusAction();

    $updatedTask = $action->execute($task, TaskStatus::IN_PROGRESS);

    expect($updatedTask->status)->toBe(TaskStatus::IN_PROGRESS);
});

test('it fires TaskCompleted event when status changes to completed', function () {
    Event::fake();

    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => TaskStatus::PENDING,
    ]);

    $action = new UpdateTaskStatusAction();
    $action->execute($task, TaskStatus::COMPLETED);

    Event::assertDispatched(TaskCompleted::class, function ($event) use ($task) {
        return $event->task->id === $task->id;
    });
});

test('it does not fire TaskCompleted event when already completed', function () {
    Event::fake();

    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => TaskStatus::COMPLETED,
    ]);

    $action = new UpdateTaskStatusAction();
    $action->execute($task, TaskStatus::COMPLETED);

    Event::assertNotDispatched(TaskCompleted::class);
});

test('it does not fire TaskCompleted event for non-completed status', function () {
    Event::fake();

    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => TaskStatus::PENDING,
    ]);

    $action = new UpdateTaskStatusAction();
    $action->execute($task, TaskStatus::IN_PROGRESS);

    Event::assertNotDispatched(TaskCompleted::class);
});
