<?php

namespace App\Services\OperationsHub;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\AgencyPartner;
use App\Models\Communication;
use App\Models\OperationsTask;
use App\Models\RateRequest;
use Carbon\CarbonInterface;

class FollowUpScheduler
{
    public function nextBusinessDate(int $days, ?CarbonInterface $from = null): CarbonInterface
    {
        $date = ($from ? $from->copy() : now())->startOfDay();

        return $date->addWeekdays($days);
    }

    public function createUniqueTask(array $attributes): OperationsTask
    {
        $match = [
            'task_type' => $attributes['task_type'],
            'supplier_id' => $attributes['supplier_id'] ?? null,
            'agency_partner_id' => $attributes['agency_partner_id'] ?? null,
            'rate_request_id' => $attributes['rate_request_id'] ?? null,
            'communication_id' => $attributes['communication_id'] ?? null,
            'status' => TaskStatus::Open,
        ];

        $existing = OperationsTask::query()->where($match)->where('title', $attributes['title'])->first();

        if ($existing) {
            return $existing;
        }

        return OperationsTask::create($attributes + [
            'priority' => $attributes['priority'] ?? TaskPriority::Normal,
            'status' => $attributes['status'] ?? TaskStatus::Open,
            'original_due_at' => $attributes['due_at'] ?? null,
        ]);
    }

    public function scheduleSupplierRateFollowUp(RateRequest $request): OperationsTask
    {
        $dueAt = $this->nextBusinessDate((int) config('operations_hub.follow_up.supplier_rate_request_business_days'));

        return $this->createUniqueTask([
            'title' => 'Follow up on supplier rate request',
            'description' => 'Follow up with the supplier on the requested rates.',
            'task_type' => TaskType::SupplierFollowUp,
            'supplier_id' => $request->supplier_id,
            'rate_request_id' => $request->id,
            'assigned_to' => $request->assigned_to,
            'due_at' => $dueAt,
        ]);
    }

    public function scheduleAgencyIntroductionFollowUp(AgencyPartner $agencyPartner): OperationsTask
    {
        $dueAt = $this->nextBusinessDate((int) config('operations_hub.follow_up.agency_intro_business_days'));

        return $this->createUniqueTask([
            'title' => 'Follow up on agency introduction',
            'description' => 'Check in with the agency partner after the introduction.',
            'task_type' => TaskType::AgencyFollowUp,
            'agency_partner_id' => $agencyPartner->id,
            'assigned_to' => $agencyPartner->assigned_to,
            'due_at' => $dueAt,
        ]);
    }

    public function scheduleCommunicationFollowUp(Communication $communication): ?OperationsTask
    {
        if (! $communication->follow_up_required || ! $communication->next_follow_up_at) {
            return null;
        }

        return $this->createUniqueTask([
            'title' => 'Follow up on communication',
            'description' => 'Follow up on the logged communication.',
            'task_type' => $communication->supplier_id ? TaskType::SupplierFollowUp : TaskType::AgencyFollowUp,
            'supplier_id' => $communication->supplier_id,
            'agency_partner_id' => $communication->agency_partner_id,
            'communication_id' => $communication->id,
            'assigned_to' => $communication->logged_by,
            'due_at' => $communication->next_follow_up_at,
        ]);
    }
}
