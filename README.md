# TaskFlow — Task Management API

A Laravel REST API with a polished frontend dashboard for managing tasks.

---

## Tech Stack
- **Backend**: PHP 8.2+, Laravel 11
- **Database**: MySQL 8+
- **Frontend**: Vanilla JS + custom CSS (served via Blade)

---

## Core Business Logic
This API strictly enforces the following internship requirements:
- [cite_start]**Priority Sorting**: `GET /api/tasks` results are ordered by High → Medium → Low, then by `due_date`[cite: 49].
- **Status Flow**: Tasks can only move `pending` → `in_progress` → `done`. [cite_start]Reverting or skipping statuses is blocked[cite: 57, 59].
- **Strict Deletion**: Only tasks marked as `done` can be deleted. [cite_start]Others return a `403 Forbidden`[cite: 64, 65].
- [cite_start]**Data Integrity**: Titles must be unique per `due_date`, and deadlines cannot be in the past[cite: 41, 43].

---

## Local Setup

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

Edit `.env` and set your MySQL credentials:
```
DB_DATABASE=task_manager
DB_USERNAME=your_mysql_user
DB_PASSWORD=your_mysql_password
```

### 3. Create Database
```sql
CREATE DATABASE task_manager;
```

### 4. Run Migrations & Seeder
```bash
php artisan migrate
php artisan db:seed          # Seeds 5 sample tasks
```

### 5. Start Server
```bash
php artisan serve
# → http://localhost:8000
```

Open `http://localhost:8000` in your browser for the UI.

---

## API Reference

Base URL: `/api`

### Create Task
```http
POST /api/tasks
Content-Type: application/json

{
  "title": "Fix critical bug",
  "due_date": "2026-04-05",
  "priority": "high"
}
```
**Rules:** title + due_date must be unique; due_date must be today or later; priority must be `low|medium|high`

---

### List Tasks
```http
GET /api/tasks
GET /api/tasks?status=pending
```
Returns tasks sorted by priority (high→low), then due_date ascending.

---

### Update Status
```http
PATCH /api/tasks/{id}/status
```
Advances status along the chain: `pending → in_progress → done`. Cannot skip or revert.

---

### Delete Task
```http
DELETE /api/tasks/{id}
```
Only `done` tasks may be deleted. Returns `403` otherwise.

---

### Daily Report (Bonus)
```http
GET /api/tasks/report?date=2026-04-01
```
```json
{
  "date": "2026-04-01",
  "summary": {
    "high":   { "pending": 2, "in_progress": 1, "done": 0 },
    "medium": { "pending": 1, "in_progress": 0, "done": 3 },
    "low":    { "pending": 0, "in_progress": 0, "done": 1 }
  }
}
```

---

## Deployment (Railway)

Railway provides free MySQL + PHP hosting and is the fastest path to a live URL.

### Steps

1. **Push to GitHub**
```bash
git init && git add . && git commit -m "Initial commit"
gh repo create task-manager --public --push
```

2. **Create Railway Project**  
   Go to [railway.app](https://railway.app) → New Project → Deploy from GitHub → select your repo.

3. **Add MySQL**  
   In your Railway project → New → Database → MySQL.  
   Railway auto-injects `MYSQL_URL`. Add these variables manually under **Variables**:
   ```
   APP_KEY=           # run: php artisan key:generate --show
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   ```

4. **Add Procfile** (tells Railway how to start)
```
web: php artisan serve --host=0.0.0.0 --port=$PORT
release: php artisan migrate --force
```

5. **Deploy** — Railway will build and run migrations automatically. Copy the generated URL.

---

## Deployment (Render)

1. New Web Service → connect GitHub repo
2. **Build command**: `composer install --no-dev && php artisan key:generate`
3. **Start command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
4. Add a **Render MySQL** database and set the same `DB_*` env vars as above.
5. Add a **Pre-deploy command**: `php artisan migrate --force`

---

## SQL Dump

To generate a dump of your local database for submission:
```bash
mysqldump -u root -p task_manager > task_manager_dump.sql
```

---

## Notes
- The `/api/tasks/report` route is declared **before** `/api/tasks/{id}` in `routes/api.php` to prevent Laravel from treating `"report"` as a numeric ID.
- Status validation is enforced in the model via a `$statusChain` map — no magic strings scattered through controllers.
- Unique constraint on `(title, due_date)` is enforced both at the DB level (migration) and via Laravel's `Rule::unique` in the Form Request.
