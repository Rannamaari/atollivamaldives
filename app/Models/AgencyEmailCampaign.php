<?php

namespace App\Models;

use App\Enums\AgencyEmailCampaignStatus;
use App\Models\Concerns\TracksUserstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyEmailCampaign extends Model
{
    use TracksUserstamps;

    protected $fillable = [
        'name',
        'email_template_id',
        'status',
        'start_date',
        'send_window_starts_at',
        'daily_limit',
        'interval_minutes',
        'agency_partner_ids',
        'agency_contact_ids',
        'partner_collection_ids',
        'manual_recipients',
        'sender_name',
        'sender_email',
        'reply_to_email',
        'subject_override',
        'body_override',
        'notes',
        'started_at',
        'paused_at',
        'completed_at',
        'stopped_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => AgencyEmailCampaignStatus::class,
            'start_date' => 'date',
            'agency_partner_ids' => 'array',
            'agency_contact_ids' => 'array',
            'partner_collection_ids' => 'array',
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'completed_at' => 'datetime',
            'stopped_at' => 'datetime',
        ];
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AgencyEmailCampaignRecipient::class, 'campaign_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
