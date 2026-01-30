# Quick Setup Guide

## 🚀 Quick Start (Docker - Recommended)

### 1. Clone & Build
```bash
git clone <repository-url>
cd task-management-system-mvc
docker-compose up -d --build
```

### 2. Install Dependencies & Setup
```bash
docker-compose exec app composer install
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app npm install
docker-compose exec app npm run build
```

### 3. Access Application
- Open: http://localhost:8080
- Register a new account
- Start managing tasks!

## 🧪 Run Tests
```bash
docker-compose exec app php artisan test
```

## 🛑 Stop Services
```bash
docker-compose down
```

---

## 🖥️ Local Development (Without Docker)

### 1. Clone & Install
```bash
git clone <repository-url>
cd task-management-system-mvc
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

### 3. Database & Assets
```bash
php artisan migrate
npm run dev
```

### 4. Start Server
```bash
php artisan serve
```

### 5. Access Application
- Open: http://localhost:8000
- Register and start using!

---

## 📊 Database Seeding (Optional)

To create sample data for testing:

```bash
# With Docker
docker-compose exec app php artisan migrate:fresh --seed

# Local
php artisan migrate:fresh --seed
```

---

## 🧪 Testing Commands

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=TaskController

# Run tests in parallel (faster)
php artisan test --parallel

# Run with coverage report
php artisan test --coverage
```

---

## 🔧 Common Docker Commands

```bash
# View application logs
docker-compose logs -f app

# Access PHP container shell
docker-compose exec app bash

# Run any Artisan command
docker-compose exec app php artisan [command]

# Run Composer commands
docker-compose exec app composer [command]

# Rebuild containers
docker-compose down && docker-compose up -d --build

# Clear Laravel cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

---

## 🐛 Troubleshooting

### Port Already in Use
If port 8080 is already in use, modify `docker-compose.yml`:
```yaml
nginx:
  ports:
    - "8081:80"  # Change 8080 to 8081
```

### Permission Issues (Docker)
```bash
sudo chown -R $USER:$USER .
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Error
Check `.env` file database settings match your Docker configuration.

---

## 📝 Default Database Configuration

### Docker (PostgreSQL):
- Host: `db`
- Port: `5432`
- Database: `task_management`
- Username: `laravel`
- Password: `secret`

### Local (SQLite):
- Database file: `database/database.sqlite`

---

## 🎯 First Steps After Setup

1. **Register a User**
   - Go to `/register`
   - Create your account

2. **Create Your First Task**
   - Navigate to "Tasks" in the menu
   - Click "Create New Task"
   - Fill in the form and submit

3. **Explore Features**
   - View all tasks (paginated list)
   - Edit task details
   - Change status/priority
   - Delete tasks

---

## 📖 For More Information

See the main [README.md](README.md) for:
- Complete feature list
- Architecture documentation
- API endpoints
- Design patterns used
- Contributing guidelines
