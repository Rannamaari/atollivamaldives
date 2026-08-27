<?php

namespace App\Models;

use App\Enums\RateRequestStatus;
use App\Models\Concerns\TracksUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RateRequest extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['supplier_id', 'supplier_contact_id', 'request_title', 'requested_rate_period', 'requested_markets', 'requested_services', 'status', 'drafted_at', 'sent_at', 'sent_by', 'first_follow_up_at', 'second_follow_up_at', 'next_follow_up_at', 'response_received_at', 'rates_received', 'agreement_received', 'notes', 'assigned_to', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'status' => RateRequestStatus::class,
            'drafted_at' => 'datetime',
            'sent_at' => 'datetime',
            'first_follow_up_at' => 'datetime',
            'second_follow_up_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'response_received_at' => 'datetime',
            'rates_received' => 'boolean',
            'agreement_received' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierContact(): BelongsTo
    {
        return $this->belongsTo(SupplierContact::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OperationsTask::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(InternalNote::class);
    }

    public function activityEvents(): HasMany
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentRecord::class, 'documentable');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
