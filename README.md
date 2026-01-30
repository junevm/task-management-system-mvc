# Task Management System - Laravel 11.x

A comprehensive, production-ready Task Management System built with Laravel 11.x, PHP 8.3+, and modern best practices. This project demonstrates professional Laravel architecture including Action classes, Events/Listeners, Policies, and comprehensive testing.

## 🏗️ Technical Stack

- **Framework**: Laravel 11.x
- **Language**: PHP 8.3+ (with strict typing, Enums, and readonly classes)
- **Database**: PostgreSQL (via Docker) or SQLite for development
- **Frontend**: Blade components with Tailwind CSS
- **Authentication**: Laravel Breeze
- **Testing**: Pest PHP with high coverage
- **Containerization**: Docker with docker-compose (Nginx, PHP-FPM, PostgreSQL, Redis)

## ✨ Features

### Core Functionality
- **Task Management (CRUD)**
  - Create, Read, Update, Delete tasks
  - Task attributes: Title, Description, Status (Enum), Priority (Enum), Due Date
  - Form Request validation for all inputs

- **User Authentication & Authorization**
  - Laravel Breeze for lightweight authentication scaffolding
  - Policy-based authorization (users can only manage their own tasks)
  - Protected routes with middleware

### Professional Architecture
- **Action Classes** - Business logic separated from controllers
  - `CreateTaskAction` - Handles task creation
  - `UpdateTaskAction` - Handles task updates
  - `UpdateTaskStatusAction` - Handles status changes with event dispatching
  - `DeleteTaskAction` - Handles task deletion

- **Events & Listeners**
  - `TaskCompleted` event fired when task status changes to "Completed"
  - `TaskCompletedListener` logs notification to demonstrate the pattern

- **Enums** (PHP 8.3)
  - `TaskStatus`: PENDING, IN_PROGRESS, COMPLETED
  - `TaskPriority`: LOW, MEDIUM, HIGH
  - Both include helper methods for labels and colors

- **Laravel Policies**
  - `TaskPolicy` ensures users can only view/edit their own tasks

## 📁 Project Structure

```
app/
├── Actions/
│   └── Tasks/                    # Business logic Action classes
│       ├── CreateTaskAction.php
│       ├── UpdateTaskAction.php
│       ├── UpdateTaskStatusAction.php
│       └── DeleteTaskAction.php
├── Enums/                        # PHP 8.3 Enums
│   ├── TaskStatus.php
│   └── TaskPriority.php
├── Events/
│   └── TaskCompleted.php         # Event fired on task completion
├── Listeners/
│   └── TaskCompletedListener.php # Logs task completion
├── Http/
│   ├── Controllers/
│   │   └── TaskController.php    # Resource controller using Actions
│   └── Requests/
│       └── Tasks/                # Form Request validation
│           ├── StoreTaskRequest.php
│           └── UpdateTaskRequest.php
├── Models/
│   ├── Task.php                  # Eloquent model with enum casts
│   └── User.php
└── Policies/
    └── TaskPolicy.php            # Authorization policy

database/
├── factories/
│   └── TaskFactory.php           # Factory for testing
└── migrations/
    └── 2026_01_30_091746_create_tasks_table.php

tests/
├── Feature/
│   ├── Actions/                  # Tests for Action classes
│   │   ├── CreateTaskActionTest.php
│   │   ├── UpdateTaskActionTest.php
│   │   ├── UpdateTaskStatusActionTest.php
│   │   └── DeleteTaskActionTest.php
│   └── Tasks/
│       ├── TaskControllerTest.php # Feature tests for endpoints
│       └── TaskPolicyTest.php     # Policy authorization tests

docker/
├── nginx/
│   └── conf.d/
│       └── default.conf          # Nginx configuration
└── php/
    └── local.ini                 # PHP configuration

resources/views/
└── tasks/                        # Blade views with Tailwind CSS
    ├── index.blade.php
    ├── create.blade.php
    ├── show.blade.php
    └── edit.blade.php
```

## 🚀 Setup Instructions

### Prerequisites
- Docker and Docker Compose installed
- Git

### Installation

#### Option 1: Using Docker (Recommended for Production)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task-management-system-mvc
   ```

2. **Build and start Docker containers**
   ```bash
   docker-compose up -d --build
   ```

3. **Install PHP dependencies**
   ```bash
   docker-compose exec app composer install
   ```

4. **Set up environment file**
   ```bash
   docker-compose exec app cp .env.example .env
   docker-compose exec app php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

6. **Build frontend assets**
   ```bash
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

7. **Access the application**
   - Open browser: `http://localhost:8080`
   - Register a new user account
   - Start managing tasks!

#### Option 2: Local Development (Using SQLite)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task-management-system-mvc
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** (Already set to SQLite in .env by default)
   ```bash
   touch database/database.sqlite
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build assets**
   ```bash
   npm run dev
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   - Open browser: `http://localhost:8000`

## 🧪 Running Tests

The project includes comprehensive Pest PHP tests with high coverage:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --filter=TaskController

# Run in parallel for faster execution
php artisan test --parallel
```

### Test Coverage
- ✅ Action Classes (Unit tests)
  - CreateTaskAction
  - UpdateTaskAction
  - UpdateTaskStatusAction
  - DeleteTaskAction
- ✅ Feature Tests
  - Task CRUD operations
  - Authorization policies
  - Event dispatching
- ✅ Policy Tests
  - User can only manage own tasks

## 🔐 Security Features

- CSRF protection on all forms
- Policy-based authorization
- Secure password hashing with bcrypt
- Input validation via Form Requests
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating

## 📝 Code Quality

This project follows:
- PSR-12 coding standards
- Strict typing (`declare(strict_types=1)`)
- Dependency injection (minimal facade usage)
- Single Responsibility Principle
- Readonly classes where appropriate
- PHP 8.3 features (Enums, Union types, etc.)

## 🔧 Docker Services

The `docker-compose.yml` includes:

1. **app** - PHP 8.3-FPM with all required extensions
2. **nginx** - Web server (exposed on port 8080)
3. **db** - PostgreSQL 16 (exposed on port 5432)
4. **redis** - Redis 7 for caching and sessions (exposed on port 6379)

### Useful Docker Commands

```bash
# View logs
docker-compose logs -f app

# Access PHP container
docker-compose exec app bash

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Run Artisan commands
docker-compose exec app php artisan [command]

# Run Composer commands
docker-compose exec app composer [command]
```

## 📚 API Endpoints

All endpoints require authentication:

- `GET /tasks` - List all tasks (paginated)
- `GET /tasks/create` - Show create form
- `POST /tasks` - Store new task
- `GET /tasks/{task}` - Show task details
- `GET /tasks/{task}/edit` - Show edit form
- `PATCH /tasks/{task}` - Update task
- `DELETE /tasks/{task}` - Delete task

## 🎨 Frontend

Built with:
- Blade components for reusability
- Tailwind CSS for styling
- Alpine.js (via Breeze) for interactivity
- Responsive design
- Accessible form controls

## 🧩 Key Design Patterns

1. **Action Pattern** - Encapsulates business logic in dedicated classes
2. **Repository Pattern** - Via Eloquent ORM
3. **Observer Pattern** - Events and Listeners
4. **Strategy Pattern** - Enum-based status/priority handling
5. **Factory Pattern** - Database factories for testing

## 📖 Additional Documentation

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Pest PHP Documentation](https://pestphp.com)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

## 🤝 Contributing

This is a demonstration project showcasing Laravel best practices. Feel free to fork and adapt for your needs.

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Author

Built with ❤️ using Laravel 11.x and modern PHP practices.
