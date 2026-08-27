<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Concerns\TracksUserstamps;
use App\Services\OperationsHub\ActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationsTask extends Model
{
    use HasFactory, TracksUserstamps;

    protected $table = 'operations_tasks';

    protected $fillable = ['title', 'description', 'task_type', 'supplier_id', 'agency_partner_id', 'rate_request_id', 'communication_id', 'assigned_to', 'created_by', 'priority', 'status', 'due_at', 'original_due_at', 'reminder_at', 'completed_at', 'completion_notes', 'snoozed_at', 'cancellation_reason'];

    protected function casts(): array
    {
        return [
            'task_type' => TaskType::class,
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'due_at' => 'datetime',
            'original_due_at' => 'datetime',
            'reminder_at' => 'datetime',
            'completed_at' => 'datetime',
            'snoozed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OperationsTask $task): void {
            if ($task->due_at && blank($task->original_due_at)) {
                $task->original_due_at = $task->due_at;
            }
        });

        static::created(function (OperationsTask $task): void {
            app(ActivityLogger::class)->log(
                ActivityEventType::TaskCreated,
                'Task created',
                $task->title,
                supplier: $task->supplier,
                agencyPartner: $task->agencyPartner,
                rateRequest: $task->rateRequest,
                task: $task,
            );
        });

        static::updated(function (OperationsTask $task): void {
            if ($task->wasChanged('completed_at') && $task->completed_at) {
                app(ActivityLogger::class)->log(
                    ActivityEventType::TaskCompleted,
                    'Task completed',
                    $task->title,
                    supplier: $task->supplier,
                    agencyPartner: $task->agencyPartner,
                    rateRequest: $task->rateRequest,
                    task: $task,
                );

                return;
            }

            if ($task->wasChanged('due_at')) {
                app(ActivityLogger::class)->log(
                    ActivityEventType::TaskUpdated,
                    'Task rescheduled',
                    $task->title,
                    supplier: $task->supplier,
                    agencyPartner: $task->agencyPartner,
                    rateRequest: $task->rateRequest,
                    task: $task,
                );
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function agencyPartner(): BelongsTo
    {
        return $this->belongsTo(AgencyPartner::class);
    }

    public function rateRequest(): BelongsTo
    {
        return $this->belongsTo(RateRequest::class);
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
