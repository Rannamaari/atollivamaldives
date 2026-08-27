<?php

namespace App\Models;

use App\Enums\ActivityEventType;
use App\Models\Concerns\TracksUserstamps;
use App\Services\OperationsHub\ActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalNote extends Model
{
    use HasFactory, TracksUserstamps;

    protected $fillable = ['supplier_id', 'agency_partner_id', 'rate_request_id', 'body', 'created_by', 'updated_by'];

    protected static function booted(): void
    {
        static::created(function (InternalNote $note): void {
            app(ActivityLogger::class)->log(
                ActivityEventType::NoteAdded,
                'Internal note added',
                str($note->body)->limit(120)->toString(),
                supplier: $note->supplier,
                agencyPartner: $note->agencyPartner,
                rateRequest: $note->rateRequest,
                note: $note,
            );
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
