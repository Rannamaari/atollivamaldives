<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use App\Enums\SupplierPartnershipStatus;
use App\Enums\SupplierType;
use App\Models\Concerns\TracksUserstamps;
use App\Services\OperationsHub\ActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['legal_name', 'trading_name', 'supplier_type', 'registration_number', 'atoll', 'island', 'country', 'website', 'general_email', 'sales_email', 'reservations_email', 'contracting_email', 'accounts_email', 'main_telephone', 'whatsapp_number', 'partnership_status', 'first_contacted_at', 'last_contacted_at', 'next_follow_up_at', 'agreement_start_date', 'agreement_expiry_date', 'rate_validity_start_date', 'rate_validity_end_date', 'assigned_to', 'internal_notes', 'is_active', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'supplier_type' => SupplierType::class,
            'partnership_status' => SupplierPartnershipStatus::class,
            'first_contacted_at' => 'date',
            'last_contacted_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'agreement_start_date' => 'date',
            'agreement_expiry_date' => 'date',
            'rate_validity_start_date' => 'date',
            'rate_validity_end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Supplier $supplier): void {
            app(ActivityLogger::class)->log(
                ActivityEventType::Created,
                'Supplier created',
                $supplier->trading_name ?: $supplier->legal_name,
                supplier: $supplier,
            );
        });

        static::updated(function (Supplier $supplier): void {
            if ($supplier->wasChanged('partnership_status')) {
                app(ActivityLogger::class)->log(
                    ActivityEventType::StatusChanged,
                    'Supplier status changed',
                    'Partnership status updated to '.$supplier->partnership_status?->label().'.',
                    supplier: $supplier,
                );
            }
        });
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class);
    }

    public function rateRequests(): HasMany
    {
        return $this->hasMany(RateRequest::class);
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

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentRecord::class, 'documentable');
    }

    public function activityEvents(): HasMany
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(PartnerCollection::class, 'partner_collection_supplier');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
