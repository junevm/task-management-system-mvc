<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;

final readonly class UpdateTaskAction
{
    public function execute(
        Task $task,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        ?string $dueDate
    ): Task {
        $task->update([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'due_date' => $dueDate,
        ]);

        return $task->fresh();
    }
}
