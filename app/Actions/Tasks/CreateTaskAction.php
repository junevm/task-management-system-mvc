<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

final readonly class CreateTaskAction
{
    public function execute(
        User $user,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        ?string $dueDate
    ): Task {
        return Task::create([
            'user_id' => $user->id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'due_date' => $dueDate,
        ]);
    }
}
