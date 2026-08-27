<?php

namespace App\Services\OperationsHub;

use App\Enums\ActivityEventType;
use App\Models\ActivityEvent;
use App\Models\AgencyPartner;
use App\Models\Communication;
use App\Models\DocumentRecord;
use App\Models\InternalNote;
use App\Models\OperationsTask;
use App\Models\RateRequest;
use App\Models\Supplier;

class ActivityLogger
{
    public function log(
        ActivityEventType $type,
        string $title,
        ?string $description = null,
        ?Supplier $supplier = null,
        ?AgencyPartner $agencyPartner = null,
        ?RateRequest $rateRequest = null,
        ?Communication $communication = null,
        ?OperationsTask $task = null,
        ?InternalNote $note = null,
        ?DocumentRecord $document = null,
        array $meta = [],
    ): ActivityEvent {
        return ActivityEvent::create([
            'supplier_id' => $supplier?->id ?? $rateRequest?->supplier_id ?? $communication?->supplier_id ?? $task?->supplier_id ?? $note?->supplier_id,
            'agency_partner_id' => $agencyPartner?->id ?? $communication?->agency_partner_id ?? $task?->agency_partner_id ?? $note?->agency_partner_id,
            'rate_request_id' => $rateRequest?->id ?? $communication?->rate_request_id ?? $task?->rate_request_id ?? $note?->rate_request_id,
            'communication_id' => $communication?->id,
            'operations_task_id' => $task?->id,
            'internal_note_id' => $note?->id,
            'document_record_id' => $document?->id,
            'user_id' => auth()->id(),
            'event_type' => $type,
            'title' => $title,
            'description' => $description,
            'meta' => $meta,
            'occurred_at' => now(),
        ]);
    }
}
