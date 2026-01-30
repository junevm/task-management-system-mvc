<?php

declare(strict_types=1);

use App\Actions\Tasks\DeleteTaskAction;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it deletes a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $action = new DeleteTaskAction();
    $result = $action->execute($task);

    expect($result)->toBeTrue()
        ->and(Task::find($task->id))->toBeNull();
});

test('it returns true on successful deletion', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $action = new DeleteTaskAction();
    $result = $action->execute($task);

    expect($result)->toBeTrue();
});
