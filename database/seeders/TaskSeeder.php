<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $nextWeek = Carbon::today()->addDays(7)->toDateString();

        DB::table('tasks')->insert([
            [
                'title'      => 'Fix critical production bug',
                'due_date'   => $today,
                'priority'   => 'high',
                'status'     => 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Write unit tests for auth module',
                'due_date'   => $tomorrow,
                'priority'   => 'high',
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Update API documentation',
                'due_date'   => $tomorrow,
                'priority'   => 'medium',
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Refactor database queries',
                'due_date'   => $nextWeek,
                'priority'   => 'medium',
                'status'     => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Clean up unused CSS files',
                'due_date'   => $nextWeek,
                'priority'   => 'low',
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
