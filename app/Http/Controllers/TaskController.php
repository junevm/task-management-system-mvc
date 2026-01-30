<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Tasks\CreateTaskAction;
use App\Actions\Tasks\DeleteTaskAction;
use App\Actions\Tasks\UpdateTaskAction;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = $request->user()->tasks()->latest()->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('tasks.create', [
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }

    public function store(StoreTaskRequest $request, CreateTaskAction $action): RedirectResponse
    {
        $task = $action->execute(
            user: $request->user(),
            title: $request->input('title'),
            description: $request->input('description'),
            status: TaskStatus::from($request->input('status')),
            priority: TaskPriority::from($request->input('priority')),
            dueDate: $request->input('due_date')
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskAction $action): RedirectResponse
    {
        $action->execute(
            task: $task,
            title: $request->input('title'),
            description: $request->input('description'),
            status: TaskStatus::from($request->input('status')),
            priority: TaskPriority::from($request->input('priority')),
            dueDate: $request->input('due_date')
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task, DeleteTaskAction $action): RedirectResponse
    {
        $this->authorize('delete', $task);

        $action->execute($task);

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
