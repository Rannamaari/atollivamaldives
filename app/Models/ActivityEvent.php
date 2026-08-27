<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityEvent extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'agency_partner_id', 'rate_request_id', 'communication_id', 'operations_task_id', 'internal_note_id', 'document_record_id', 'user_id', 'event_type', 'title', 'description', 'meta', 'occurred_at'];

    protected function casts(): array
    {
        return [
            'event_type' => ActivityEventType::class,
            'meta' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
