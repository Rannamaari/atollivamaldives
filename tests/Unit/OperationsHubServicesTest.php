<?php

namespace Tests\Unit;

use App\Enums\DocumentType;
use App\Enums\EmailTemplateType;
use App\Models\Communication;
use App\Models\DocumentRecord;
use App\Models\EmailTemplate;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\User;
use App\Services\OperationsHub\CommunicationDraftFactory;
use App\Services\OperationsHub\TemplateRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperationsHubServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_generating_a_draft_does_not_mark_anything_as_sent(): void
    {
        $supplier = Supplier::factory()->create(['trading_name' => 'Atoll Reef Resort']);
        $template = EmailTemplate::factory()->create([
            'subject_template' => 'Hello {{supplier_name}}',
            'body_template' => 'Rates for {{rate_period}}',
        ]);

        $result = app(TemplateRenderer::class)->render($template, ['supplier' => $supplier]);

        $this->assertSame('Hello Atoll Reef Resort', $result['subject']);
        $this->assertContains('{{rate_period}}', $result['unresolved']);
    }

    public function test_template_content_cannot_execute_code(): void
    {
        $template = EmailTemplate::factory()->create([
            'subject_template' => '{{atolliva_name}}',
            'body_template' => '{{ phpinfo() }} {{supplier_name}}',
        ]);

        $result = app(TemplateRenderer::class)->render($template, []);

        $this->assertStringContainsString('{{ phpinfo() }}', $result['body']);
    }

    public function test_confidential_documents_cannot_be_downloaded_without_authorization(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('rates.pdf', 200, 'application/pdf');
        $path = $file->store('operations/documents', 'local');

        $document = DocumentRecord::create([
            'documentable_type' => Supplier::class,
            'documentable_id' => Supplier::factory()->create()->id,
            'document_type' => DocumentType::RateSheet,
            'original_filename' => 'rates.pdf',
            'stored_filename' => basename($path),
            'stored_path' => $path,
            'mime_type' => 'application/pdf',
            'file_size' => Storage::disk('local')->size($path),
            'is_confidential' => true,
        ]);

        $this->get(route('operations.documents.download', $document))
            ->assertForbidden();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('operations.documents.download', $document))
            ->assertOk();
    }

    public function test_rate_request_draft_creation_stays_as_a_draft_and_links_everything(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['trading_name' => 'Atoll Reef Resort']);
        $contact = SupplierContact::create([
            'supplier_id' => $supplier->id,
            'full_name' => 'Reservations Team',
            'email' => 'reservations@example.com',
            'is_primary' => true,
        ]);
        $rateRequest = RateRequest::create([
            'supplier_id' => $supplier->id,
            'supplier_contact_id' => $contact->id,
            'request_title' => '2027 contract rates',
            'requested_rate_period' => 'Summer 2027',
        ]);
        EmailTemplate::factory()->create([
            'template_type' => EmailTemplateType::RequestB2BRates,
            'subject_template' => 'Request for {{property_name}}',
            'body_template' => 'Please share rates for {{rate_period}}',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $draft = app(CommunicationDraftFactory::class)->createDraft(
            supplier: $supplier,
            rateRequest: $rateRequest,
            contact: $contact,
            templateType: EmailTemplateType::RequestB2BRates,
        );

        $this->assertInstanceOf(Communication::class, $draft);
        $this->assertSame($supplier->id, $draft->supplier_id);
        $this->assertSame($rateRequest->id, $draft->rate_request_id);
        $this->assertSame($contact->id, $draft->supplier_contact_id);
        $this->assertSame('[ATL-RATE-'.str_pad((string) $rateRequest->id, 4, '0', STR_PAD_LEFT).'] Greetings from Atolliva Maldives | Request for Atoll Reef Resort', $draft->subject);
        $this->assertSame('draft', $draft->status->value);
        $this->assertNull($supplier->fresh()->last_contacted_at);
    }
}
