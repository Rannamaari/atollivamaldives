<?php

namespace App\Models;

use App\Enums\AgencyEmailRecipientStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyEmailCampaignRecipient extends Model
{
    protected $fillable = [
        'campaign_id',
        'agency_partner_id',
        'agency_contact_id',
        'communication_id',
        'recipient_email',
        'recipient_name',
        'status',
        'scheduled_for',
        'sent_at',
        'failure_reason',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'status' => AgencyEmailRecipientStatus::class,
            'scheduled_for' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AgencyEmailCampaign::class, 'campaign_id');
    }

    public function agencyPartner(): BelongsTo
    {
        return $this->belongsTo(AgencyPartner::class);
    }

    public function agencyContact(): BelongsTo
    {
        return $this->belongsTo(AgencyContact::class);
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
