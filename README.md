# TaskFlow — Task Management API

A professional Laravel 11 REST API designed for efficient task management, featuring strict business logic enforcement and a clean JSON interface.

---

## 🚀 Core Business Logic

This API is built to satisfy specific internship requirements:

- **Priority-First Sorting**: The `GET /api/tasks` endpoint automatically orders tasks by **High → Medium → Low**, then by `due_date` ascending.
- **Strict Status Workflow**: Tasks can only progress forward: `pending` → `in_progress` → `done`. The system prevents skipping or reverting statuses.
- **Deletion Security**: Only tasks marked as `done` can be deleted. Any attempt to delete unfinished tasks returns a `403 Forbidden` response.
- **Data Integrity**: Enforces unique task titles per due date and prevents setting deadlines in the past.

---

## 🛠️ Local Setup

### 1. Clone & Install

```bash
git clone <your-repo-url>
cd task-manager
composer install
```

### 2. Configure Environment

Copy the template and generate your unique application key:

```bash
cp .env.example .env
php artisan key:generate
```

> **Note:** Update the `DB_*` variables in your `.env` file with your local MySQL credentials.

### 3. Database Initialization

Create your database (default: `task_manager`) and run the migrations/seeders:

```bash
php artisan migrate --seed
```

This command sets up the `tasks` table and populates it with sample data for immediate testing.

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
```

Supports optional status filter: `/api/tasks?status=in_progress`

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

Moves the task to the next logical stage in the workflow.

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

## ☁️ Deployment Instructions

### Railway *(Recommended)*

1. **Push to GitHub** — Ensure your code is in a public repository.
2. **Connect** — Link your repository to a new Railway project.
3. **Database** — Add a MySQL service to the project.
4. **Environment Variables** — Set `APP_KEY`, `DB_HOST`, `DB_USERNAME`, and `DB_PASSWORD` in the Railway Variables tab.
5. **Procfile** — This project includes a `Procfile` configured for production:
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```

---

## 📝 Technical Notes

- **Route Architecture**: API routes are strategically structured to avoid segment conflicts between the static `/report` endpoint and dynamic task IDs.
- **Validation**: All business rules are enforced via Laravel Form Requests and Eloquent model logic for maximum maintainability.
