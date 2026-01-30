<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Support\Facades\Log;

class TaskCompletedListener
{
    public function handle(TaskCompleted $event): void
    {
        Log::info('Task completed notification', [
            'task_id' => $event->task->id,
            'task_title' => $event->task->title,
            'user_id' => $event->task->user_id,
            'completed_at' => now()->toDateTimeString(),
        ]);
    }
}
