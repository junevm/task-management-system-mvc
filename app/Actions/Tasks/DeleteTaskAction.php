<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Task;

final readonly class DeleteTaskAction
{
    public function execute(Task $task): bool
    {
        return $task->delete();
    }
}
