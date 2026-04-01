<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    // ─── POST /api/tasks ──────────────────────────────────────────────────────

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title'    => $request->title,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status'   => 'pending',
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => $task,
        ], 201);
    }

    // ─── GET /api/tasks ───────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|in:pending,in_progress,done',
        ]);

        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by priority (high → medium → low), then due_date ascending
        $tasks = $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found.',
                'data'    => [],
            ]);
        }

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'count'   => $tasks->count(),
            'data'    => $tasks,
        ]);
    }

    // ─── PATCH /api/tasks/{id}/status ─────────────────────────────────────────

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        $nextStatus = $task->nextStatus();

        if ($nextStatus === null) {
            return response()->json([
                'message' => 'Task is already marked as done. No further status progression is possible.',
            ], 422);
        }

        $task->status = $nextStatus;
        $task->save();

        return response()->json([
            'message' => "Task status updated to '{$nextStatus}'.",
            'data'    => $task,
        ]);
    }

    // ─── DELETE /api/tasks/{id} ───────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        if ($task->status !== 'done') {
            return response()->json([
                'message' => 'Only tasks with status "done" can be deleted.',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    // ─── GET /api/tasks/report?date=YYYY-MM-DD (Bonus) ────────────────────────

    public function report(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
        ]);

        $date = $request->date;

        $tasks = Task::whereDate('due_date', $date)->get();

        $priorities = ['high', 'medium', 'low'];
        $statuses   = ['pending', 'in_progress', 'done'];

        $summary = [];

        foreach ($priorities as $priority) {
            foreach ($statuses as $status) {
                $summary[$priority][$status] = $tasks
                    ->where('priority', $priority)
                    ->where('status', $status)
                    ->count();
            }
        }

        return response()->json([
            'date'    => $date,
            'summary' => $summary,
        ]);
    }
}
