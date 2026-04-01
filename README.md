# TaskFlow — Task Management API

A professional Laravel 11 REST API designed for efficient task management, featuring strict business logic enforcement and a clean JSON interface.

---

## 🚀 Core Business Logic

- **Priority-First Sorting**: The `GET /api/tasks` endpoint automatically orders tasks by **High → Medium → Low**, then by `due_date` ascending.
- **Strict Status Workflow**: Tasks can only progress forward: `pending` → `in_progress` → `done`. No skipping or reverting statuses.
- **Deletion Security**: Only tasks with a `done` status can be deleted. Anything else returns `403 Forbidden`.
- **Data Integrity**: Unique task titles per due date are enforced, and past due dates are rejected.

---

## 🛠️ Local Setup

### 1. Clone & Install

```bash
git clone <your-repo-url>
cd task-manager
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

> **Note:** The `DB_*` variables in `.env` need to reflect your local MySQL credentials.

### 3. Database Initialization

```bash
php artisan migrate --seed
```

Seeds the `tasks` table with sample data for immediate testing.

### 4. Start Server

```bash
php artisan serve
```

Visit `http://localhost:8000` to see the application.

---

## 📖 API Reference & Quick Tests

### List All Tasks

```
GET /api/tasks
GET /api/tasks?status=in_progress
```

### Create a New Task

```
POST /api/tasks
```

```bash
curl -X POST http://localhost:8000/api/tasks \
     -H "Content-Type: application/json" \
     -d '{"title":"Final Submission","due_date":"2026-04-01","priority":"high"}'
```

### Advance Task Status

```
PATCH /api/tasks/{id}/status
```

### Delete Completed Task

```
DELETE /api/tasks/{id}
```

### Daily Status Report *(Bonus)*

```
GET /api/tasks/report?date=YYYY-MM-DD
```

```bash
curl -X GET "http://localhost:8000/api/tasks/report?date=2026-04-01"
```

---

## ☁️ Deployment — Railway

1. Repo on GitHub (public)
2. New project on [railway.app](https://railway.app) → connect the repo
3. MySQL service added to the project
4. Environment variables set in the Railway Variables tab: `APP_KEY`, `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`
5. The `Procfile` is already configured:
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```

---

## 📝 Technical Notes

- **Route Order**: `/report` is registered before `/{id}` in `api.php` — otherwise Laravel treats the string `"report"` as a task ID.
- **Validation**: Business rules live in Form Requests and the `Task` model, not scattered across controllers.
