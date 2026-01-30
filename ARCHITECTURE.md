# Architecture Documentation

## Overview

This Task Management System is built using Laravel 11.x following modern best practices and professional design patterns. The architecture emphasizes separation of concerns, testability, and maintainability.

## Core Design Principles

### 1. Single Responsibility Principle (SRP)
Each class has one clear responsibility:
- **Controllers** - Handle HTTP requests/responses
- **Actions** - Encapsulate business logic
- **Models** - Represent data and relationships
- **Policies** - Handle authorization logic
- **Form Requests** - Validate incoming data

### 2. Dependency Injection
We avoid "facade abuse" and use proper dependency injection:

```php
public function store(
    StoreTaskRequest $request,
    CreateTaskAction $action  // Injected dependency
): RedirectResponse {
    $task = $action->execute(...);
    return redirect()->route('tasks.show', $task);
}
```

### 3. Strict Typing
All files use PHP 8.3 strict typing:

```php
<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

final readonly class CreateTaskAction
{
    public function execute(
        User $user,
        string $title,
        ?string $description,
        // ...
    ): Task {
        // Implementation
    }
}
```

## Architectural Layers

### Presentation Layer (Views)
- **Location**: `resources/views/`
- **Technology**: Blade templates with Tailwind CSS
- **Responsibilities**:
  - Display data to users
  - Collect user input
  - Client-side validation feedback

### HTTP Layer (Controllers)
- **Location**: `app/Http/Controllers/`
- **Responsibilities**:
  - Route handling
  - Request validation (via Form Requests)
  - Authorization checks (via Policies)
  - Delegate to Action classes
  - Return responses

**Example:**
```php
public function store(StoreTaskRequest $request, CreateTaskAction $action): RedirectResponse
{
    // Validation handled by StoreTaskRequest
    // Business logic delegated to CreateTaskAction
    $task = $action->execute(...);
    
    return redirect()->route('tasks.show', $task)
        ->with('success', 'Task created successfully.');
}
```

### Business Logic Layer (Actions)
- **Location**: `app/Actions/Tasks/`
- **Pattern**: Action/Command Pattern
- **Responsibilities**:
  - Execute business operations
  - Handle complex logic
  - Fire events when needed
  - Return results

**Why Actions?**
1. **Testability** - Can test business logic without HTTP layer
2. **Reusability** - Can be called from controllers, jobs, commands
3. **Single Responsibility** - Each action does one thing well
4. **Clean Controllers** - Controllers remain thin

**Example:**
```php
final readonly class UpdateTaskStatusAction
{
    public function execute(Task $task, TaskStatus $status): Task
    {
        $previousStatus = $task->status;
        $task->update(['status' => $status]);

        // Fire event when task completed
        if ($status === TaskStatus::COMPLETED && $previousStatus !== TaskStatus::COMPLETED) {
            event(new TaskCompleted($task));
        }

        return $task->fresh();
    }
}
```

### Data Layer (Models & Repositories)
- **Location**: `app/Models/`
- **Pattern**: Active Record (via Eloquent)
- **Responsibilities**:
  - Define data structure
  - Define relationships
  - Cast attributes (Enums)
  - Query scopes

**Example:**
```php
class Task extends Model
{
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,      // Enum casting
            'priority' => TaskPriority::class,  // Enum casting
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## Key Components

### Enums (PHP 8.3)

**Location**: `app/Enums/`

Enums provide type safety and encapsulation:

```php
enum TaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'blue',
            self::COMPLETED => 'green',
        };
    }
}
```

**Benefits:**
- Type safety (can't pass invalid status)
- Autocomplete in IDE
- Refactoring safety
- Helper methods for presentation

### Form Requests

**Location**: `app/Http/Requests/Tasks/`

Validation logic separated from controllers:

```php
class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::enum(TaskStatus::class)],
            'priority' => ['required', 'string', Rule::enum(TaskPriority::class)],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
```

### Policies

**Location**: `app/Policies/`

Authorization logic centralized:

```php
class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }
}
```

**Usage in Controller:**
```php
public function show(Task $task): View
{
    $this->authorize('view', $task);
    return view('tasks.show', compact('task'));
}
```

### Events & Listeners

**Pattern**: Observer Pattern

**Event** (`app/Events/TaskCompleted.php`):
```php
class TaskCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public Task $task) {}
}
```

**Listener** (`app/Listeners/TaskCompletedListener.php`):
```php
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
```

**Benefits:**
- Decoupled components
- Easy to add new listeners
- Async processing possible (queued listeners)
- Testable with Event::fake()

## Testing Strategy

### Test Structure

```
tests/
├── Feature/
│   ├── Actions/           # Test Action classes
│   └── Tasks/             # Test HTTP endpoints & policies
└── Unit/
    └── ExampleTest.php
```

### Testing Approach

1. **Action Tests** - Test business logic directly
   ```php
   test('it creates a task with all fields', function () {
       $action = new CreateTaskAction();
       $task = $action->execute(...);
       
       expect($task)->toBeInstanceOf(Task::class)
           ->and($task->title)->toBe('Test Task');
   });
   ```

2. **Feature Tests** - Test full HTTP flow
   ```php
   test('user can create a task', function () {
       $response = $this->actingAs($user)
           ->post(route('tasks.store'), $taskData);
       
       $response->assertRedirect();
       $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
   });
   ```

3. **Policy Tests** - Test authorization
   ```php
   test('user can only view own tasks', function () {
       expect($user1->can('view', $task1))->toBeTrue()
           ->and($user1->can('view', $task2))->toBeFalse();
   });
   ```

## Database Design

### Tasks Table Schema

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('status')->default('pending');
    $table->string('priority')->default('medium');
    $table->date('due_date')->nullable();
    $table->timestamps();
});
```

**Design Decisions:**
- `user_id` foreign key with cascade delete (user deletion removes their tasks)
- `status` and `priority` stored as strings (enum values)
- `description` nullable (optional field)
- `due_date` nullable (tasks may not have deadlines)
- `timestamps()` for created_at and updated_at tracking

## Security Considerations

### 1. Authorization
- Policies enforce ownership (users only access their tasks)
- Authorization checked before any action

### 2. Validation
- Form Requests validate all input
- Enum validation ensures valid status/priority
- Date validation prevents past due dates

### 3. SQL Injection Protection
- Eloquent ORM with parameterized queries
- No raw SQL queries without bindings

### 4. CSRF Protection
- All forms include CSRF tokens
- Laravel middleware validates tokens

### 5. XSS Protection
- Blade templates auto-escape output
- Use `{{ }}` instead of `{!! !!}`

## Performance Considerations

### 1. Database Queries
```php
// Paginated queries to avoid loading all tasks
$tasks = $request->user()->tasks()->latest()->paginate(10);
```

### 2. Eager Loading
```php
// Load relationships when needed to avoid N+1 queries
$tasks = Task::with('user')->get();
```

### 3. Caching (Redis)
- Session storage
- Cache store
- Queue driver (for async processing)

### 4. Asset Compilation
- Vite for frontend asset bundling
- Tailwind CSS with purge for minimal CSS

## Deployment Considerations

### Docker Stack
- **PHP-FPM**: Application server
- **Nginx**: Web server (reverse proxy)
- **PostgreSQL**: Database
- **Redis**: Cache & sessions

### Environment Configuration
- `.env` for environment-specific settings
- `.env.example` as template
- Sensitive data never committed

### Scaling Possibilities
1. **Horizontal Scaling**: Multiple app containers behind load balancer
2. **Database**: Read replicas for queries
3. **Queue Workers**: Background job processing
4. **Cache**: Redis cluster for distributed caching

## Code Standards

### PSR-12 Compliance
- 4 spaces indentation
- Opening braces on same line (methods)
- Type declarations
- Proper spacing

### Laravel Best Practices
- Service container for dependency injection
- Eloquent relationships over manual queries
- Route model binding
- Named routes
- Middleware for cross-cutting concerns

### PHP 8.3 Features Used
- Strict typing (`declare(strict_types=1)`)
- Readonly classes
- Enums with methods
- Constructor property promotion
- Named arguments

## Future Enhancements

### Potential Additions
1. **Task Categories/Tags** - Organize tasks
2. **Task Assignments** - Share tasks with other users
3. **Comments** - Discuss tasks
4. **File Attachments** - Attach documents
5. **Notifications** - Real-time updates
6. **API** - RESTful API with Laravel Sanctum
7. **Task Templates** - Reusable task patterns
8. **Reporting** - Analytics dashboard
9. **Search** - Full-text search with Algolia/Meilisearch
10. **Reminders** - Email/SMS notifications for due dates

## Conclusion

This architecture provides:
- **Maintainability**: Clear separation of concerns
- **Testability**: Comprehensive test coverage
- **Scalability**: Docker-based infrastructure
- **Security**: Multiple layers of protection
- **Developer Experience**: Modern PHP features and patterns

The codebase serves as a reference implementation for professional Laravel development, demonstrating how to build production-ready applications with modern best practices.
