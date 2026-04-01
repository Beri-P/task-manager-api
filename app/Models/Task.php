<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Priority order map for sorting (high → medium → low).
     */
    public static array $priorityOrder = [
        'high'   => 1,
        'medium' => 2,
        'low'    => 3,
    ];

    /**
     * Valid status progression chain.
     */
    public static array $statusChain = [
        'pending'     => 'in_progress',
        'in_progress' => 'done',
    ];

    /**
     * Returns the next valid status, or null if already at the end.
     */
    public function nextStatus(): ?string
    {
        return self::$statusChain[$this->status] ?? null;
    }
}
