<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use App\Enums\AgencyPartnershipStatus;
use App\Enums\AgencyRiskLevel;
use App\Models\Concerns\TracksUserstamps;
use App\Services\OperationsHub\ActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AgencyPartner extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['legal_company_name', 'trading_name', 'country', 'city', 'website', 'licence_number', 'target_customer_segment', 'source_markets', 'estimated_booking_volume', 'preferred_products', 'preferred_currency', 'commercial_arrangement', 'payment_terms', 'agreement_status', 'partnership_status', 'first_contacted_at', 'last_contacted_at', 'next_follow_up_at', 'assigned_to', 'risk_level', 'internal_notes', 'is_active', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'partnership_status' => AgencyPartnershipStatus::class,
            'risk_level' => AgencyRiskLevel::class,
            'first_contacted_at' => 'date',
            'last_contacted_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (AgencyPartner $agencyPartner): void {
            app(ActivityLogger::class)->log(
                ActivityEventType::Created,
                'Agency partner created',
                $agencyPartner->trading_name ?: $agencyPartner->legal_company_name,
                agencyPartner: $agencyPartner,
            );
        });

        static::updated(function (AgencyPartner $agencyPartner): void {
            if ($agencyPartner->wasChanged('partnership_status')) {
                app(ActivityLogger::class)->log(
                    ActivityEventType::StatusChanged,
                    'Agency status changed',
                    'Partnership status updated to '.$agencyPartner->partnership_status?->label().'.',
                    agencyPartner: $agencyPartner,
                );
            }
        });
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(AgencyContact::class);
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

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
