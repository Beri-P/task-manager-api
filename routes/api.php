<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Note: /api/tasks/report must be declared BEFORE /api/tasks/{id}
| so Laravel's router doesn't treat "report" as a dynamic {id} segment.
*/

// Bonus: Daily report
Route::get('/tasks/report', [TaskController::class, 'report']);

// CRUD
Route::post('/tasks',              [TaskController::class, 'store']);
Route::get('/tasks',               [TaskController::class, 'index']);
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
Route::delete('/tasks/{id}',       [TaskController::class, 'destroy']);
