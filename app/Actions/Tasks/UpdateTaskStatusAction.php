<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Enums\TaskStatus;
use App\Events\TaskCompleted;
use App\Models\Task;

final readonly class UpdateTaskStatusAction
{
    public function execute(Task $task, TaskStatus $status): Task
    {
        $previousStatus = $task->status;

        $task->update(['status' => $status]);

        if ($status === TaskStatus::COMPLETED && $previousStatus !== TaskStatus::COMPLETED) {
            event(new TaskCompleted($task));
        }

        return $task->fresh();
    }
}
