<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Enums\TaskType;
use App\Models\Concerns\TracksUserstamps;
use App\Services\OperationsHub\ActivityLogger;
use App\Services\OperationsHub\FollowUpScheduler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Communication extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['supplier_id', 'supplier_contact_id', 'agency_partner_id', 'agency_contact_id', 'rate_request_id', 'direction', 'channel', 'subject', 'body', 'recipient', 'status', 'drafted_at', 'occurred_at', 'follow_up_required', 'next_follow_up_at', 'attachment_paths', 'logged_by', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'direction' => CommunicationDirection::class,
            'channel' => CommunicationChannel::class,
            'status' => CommunicationStatus::class,
            'drafted_at' => 'datetime',
            'occurred_at' => 'datetime',
            'follow_up_required' => 'boolean',
            'next_follow_up_at' => 'datetime',
            'attachment_paths' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Communication $communication): void {
            if ($communication->status === CommunicationStatus::Draft) {
                return;
            }

            $timestamp = $communication->occurred_at ?? now();

            $communication->supplier?->update([
                'last_contacted_at' => $timestamp,
                'next_follow_up_at' => $communication->next_follow_up_at,
            ]);

            $communication->agencyPartner?->update([
                'last_contacted_at' => $timestamp,
                'next_follow_up_at' => $communication->next_follow_up_at,
            ]);

            if ($communication->follow_up_required && $communication->next_follow_up_at) {
                app(FollowUpScheduler::class)->createUniqueTask([
                    'title' => 'Follow up on communication',
                    'description' => 'Follow up on the logged communication.',
                    'task_type' => $communication->supplier_id ? TaskType::SupplierFollowUp : TaskType::AgencyFollowUp,
                    'supplier_id' => $communication->supplier_id,
                    'agency_partner_id' => $communication->agency_partner_id,
                    'communication_id' => $communication->id,
                    'rate_request_id' => $communication->rate_request_id,
                    'assigned_to' => $communication->logged_by,
                    'due_at' => $communication->next_follow_up_at,
                ]);
            }

            if ($communication->wasRecentlyCreated) {
                app(ActivityLogger::class)->log(
                    ActivityEventType::CommunicationLogged,
                    'Communication logged',
                    $communication->subject ?: $communication->channel?->label(),
                    supplier: $communication->supplier,
                    agencyPartner: $communication->agencyPartner,
                    rateRequest: $communication->rateRequest,
                    communication: $communication,
                );
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierContact(): BelongsTo
    {
        return $this->belongsTo(SupplierContact::class);
    }

    public function agencyPartner(): BelongsTo
    {
        return $this->belongsTo(AgencyPartner::class);
    }

    public function agencyContact(): BelongsTo
    {
        return $this->belongsTo(AgencyContact::class);
    }

    public function rateRequest(): BelongsTo
    {
        return $this->belongsTo(RateRequest::class);
    }

    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
