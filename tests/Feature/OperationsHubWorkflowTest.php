<?php

namespace Tests\Feature;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Enums\EmailTemplateType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\AgencyPartner;
use App\Models\Communication;
use App\Models\EmailTemplate;
use App\Models\OperationsTask;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\User;
use App\Services\OperationsHub\CommunicationDraftFactory;
use App\Services\OperationsHub\RateRequestWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsHubWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_have_multiple_contacts_but_only_one_primary(): void
    {
        $supplier = Supplier::factory()->create();

        $first = SupplierContact::create([
            'supplier_id' => $supplier->id,
            'full_name' => 'Primary One',
            'is_primary' => true,
        ]);

        $second = SupplierContact::create([
            'supplier_id' => $supplier->id,
            'full_name' => 'Primary Two',
            'is_primary' => true,
        ]);

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
        $this->assertCount(2, $supplier->fresh()->contacts);
    }

    public function test_agency_partner_can_be_created(): void
    {
        $agency = AgencyPartner::factory()->create([
            'legal_company_name' => 'Oceanic Travel GmbH',
        ]);

        $this->assertDatabaseHas('agency_partners', [
            'id' => $agency->id,
            'legal_company_name' => 'Oceanic Travel GmbH',
        ]);
    }

    public function test_marking_rate_request_sent_records_timestamp_user_and_one_follow_up_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $rateRequest = RateRequest::factory()->create([
            'assigned_to' => $user->id,
        ]);

        app(RateRequestWorkflow::class)->markSent($rateRequest);
        app(RateRequestWorkflow::class)->markSent($rateRequest->fresh());

        $rateRequest = $rateRequest->fresh();

        $this->assertNotNull($rateRequest->sent_at);
        $this->assertSame($user->id, $rateRequest->sent_by);
        $this->assertDatabaseCount('operations_tasks', 1);
        $this->assertDatabaseHas('operations_tasks', [
            'rate_request_id' => $rateRequest->id,
            'status' => TaskStatus::Open->value,
        ]);
    }

    public function test_logging_communication_updates_last_contacted_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $supplier = Supplier::factory()->create();

        Communication::create([
            'supplier_id' => $supplier->id,
            'direction' => CommunicationDirection::Outbound,
            'channel' => CommunicationChannel::WhatsApp,
            'status' => CommunicationStatus::SentManually,
            'subject' => 'Follow-up message',
            'occurred_at' => now(),
            'logged_by' => $user->id,
        ]);

        $this->assertNotNull($supplier->fresh()->last_contacted_at);
    }

    public function test_supplier_intro_draft_defaults_to_follow_up_required(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $supplier = Supplier::factory()->create();

        EmailTemplate::create([
            'name' => 'Supplier Introduction',
            'slug' => 'supplier-introduction',
            'template_type' => EmailTemplateType::SupplierIntroduction,
            'subject_template' => 'Introduction from {{atolliva_name}}',
            'body_template' => 'Hello {{supplier_name}}',
            'is_active' => true,
        ]);

        $draft = app(CommunicationDraftFactory::class)->createDraft(
            supplier: $supplier,
            templateType: EmailTemplateType::SupplierIntroduction,
        );

        $this->assertSame(CommunicationStatus::Draft, $draft->status);
        $this->assertTrue($draft->follow_up_required);
        $this->assertNotNull($draft->next_follow_up_at);
        $this->assertStringStartsWith('[ATL-SUP-', $draft->subject);
        $this->assertStringContainsString('Greetings from Atolliva Maldives', $draft->subject);
        $this->assertStringContainsString('Greetings from Atolliva Maldives.', $draft->body);
    }

    public function test_marking_draft_sent_with_follow_up_creates_task(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $supplier = Supplier::factory()->create();

        $communication = Communication::create([
            'supplier_id' => $supplier->id,
            'direction' => CommunicationDirection::Outbound,
            'channel' => CommunicationChannel::EmailDraft,
            'status' => CommunicationStatus::Draft,
            'subject' => 'Supplier introduction',
            'body' => 'Hello',
            'logged_by' => $user->id,
        ]);

        $communication->update([
            'status' => CommunicationStatus::SentManually,
            'occurred_at' => now(),
            'follow_up_required' => true,
            'next_follow_up_at' => now()->addDays(2),
        ]);

        $this->assertDatabaseHas('operations_tasks', [
            'communication_id' => $communication->id,
            'supplier_id' => $supplier->id,
            'status' => TaskStatus::Open->value,
        ]);
    }

    public function test_supplier_intro_draft_has_fallback_subject_and_body_without_templates(): void
    {
        $user = User::factory()->create(['name' => 'Munaad']);
        $this->actingAs($user);

        $supplier = Supplier::factory()->create([
            'legal_name' => 'Kaani Grand View',
        ]);

        $contact = SupplierContact::create([
            'supplier_id' => $supplier->id,
            'full_name' => 'Reservations Team',
            'email' => 'reservations@example.com',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $draft = app(CommunicationDraftFactory::class)->createDraft(
            supplier: $supplier,
            contact: $contact,
            templateType: EmailTemplateType::SupplierIntroduction,
        );

        $this->assertSame('[ATL-SUP-'.str_pad((string) $supplier->id, 4, '0', STR_PAD_LEFT).'] Greetings from Atolliva Maldives', $draft->subject);
        $this->assertStringContainsString('Hello Reservations Team,', $draft->body);
        $this->assertStringContainsString($supplier->trading_name ?: $supplier->legal_name, $draft->body);
        $this->assertStringContainsString('Kind regards,', $draft->body);
    }

    public function test_task_completion_and_rescheduling_preserve_original_due_date(): void
    {
        $task = OperationsTask::create([
            'title' => 'Call supplier',
            'task_type' => TaskType::Call,
            'priority' => TaskPriority::Normal,
            'status' => TaskStatus::Open,
            'due_at' => now()->addDay(),
        ]);

        $originalDueAt = $task->original_due_at;

        $task->update([
            'due_at' => now()->addDays(3),
            'snoozed_at' => now(),
        ]);

        $task->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->assertNotNull($task->fresh()->completed_at);
        $this->assertTrue($task->fresh()->original_due_at->equalTo($originalDueAt));
    }
}
