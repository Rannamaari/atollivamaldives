<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name');
            $table->string('trading_name')->nullable();
            $table->string('supplier_type')->index();
            $table->string('registration_number')->nullable();
            $table->string('atoll')->nullable()->index();
            $table->string('island')->nullable()->index();
            $table->string('country')->default('Maldives');
            $table->string('website')->nullable();
            $table->string('general_email')->nullable();
            $table->string('sales_email')->nullable();
            $table->string('reservations_email')->nullable();
            $table->string('accounts_email')->nullable();
            $table->string('main_telephone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('partnership_status')->default('not_contacted')->index();
            $table->date('first_contacted_at')->nullable();
            $table->dateTime('last_contacted_at')->nullable()->index();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->date('agreement_start_date')->nullable();
            $table->date('agreement_expiry_date')->nullable()->index();
            $table->date('rate_validity_start_date')->nullable();
            $table->date('rate_validity_end_date')->nullable()->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('full_name');
            $table->string('job_title')->nullable();
            $table->string('department')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('agency_partners', function (Blueprint $table) {
            $table->id();
            $table->string('legal_company_name');
            $table->string('trading_name')->nullable();
            $table->string('country')->default('Maldives')->index();
            $table->string('city')->nullable();
            $table->string('website')->nullable();
            $table->string('licence_number')->nullable();
            $table->string('target_customer_segment')->nullable();
            $table->text('source_markets')->nullable();
            $table->string('estimated_booking_volume')->nullable();
            $table->text('preferred_products')->nullable();
            $table->string('preferred_currency', 3)->nullable();
            $table->text('commercial_arrangement')->nullable();
            $table->text('payment_terms')->nullable();
            $table->string('agreement_status')->nullable()->index();
            $table->string('partnership_status')->default('prospect_identified')->index();
            $table->date('first_contacted_at')->nullable();
            $table->dateTime('last_contacted_at')->nullable()->index();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('risk_level')->default('not_assessed')->index();
            $table->text('internal_notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('agency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_partner_id')->nullable()->constrained('agency_partners')->nullOnDelete();
            $table->string('full_name');
            $table->string('position')->nullable();
            $table->string('department')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('rate_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('supplier_contact_id')->nullable()->constrained('supplier_contacts')->nullOnDelete();
            $table->string('request_title');
            $table->string('requested_rate_period')->nullable();
            $table->string('requested_markets')->nullable();
            $table->text('requested_services')->nullable();
            $table->string('status')->default('draft')->index();
            $table->dateTime('drafted_at')->nullable();
            $table->dateTime('sent_at')->nullable()->index();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('first_follow_up_at')->nullable()->index();
            $table->dateTime('second_follow_up_at')->nullable()->index();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->dateTime('response_received_at')->nullable()->index();
            $table->boolean('rates_received')->default(false)->index();
            $table->boolean('agreement_received')->default(false)->index();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('template_type')->index();
            $table->string('subject_template');
            $table->longText('body_template');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('supplier_contact_id')->nullable()->constrained('supplier_contacts')->nullOnDelete();
            $table->foreignId('agency_partner_id')->nullable()->constrained('agency_partners')->nullOnDelete();
            $table->foreignId('agency_contact_id')->nullable()->constrained('agency_contacts')->nullOnDelete();
            $table->foreignId('rate_request_id')->nullable()->constrained('rate_requests')->nullOnDelete();
            $table->string('direction')->index();
            $table->string('channel')->index();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->string('recipient')->nullable();
            $table->string('status')->default('draft')->index();
            $table->dateTime('drafted_at')->nullable()->index();
            $table->dateTime('occurred_at')->nullable()->index();
            $table->boolean('follow_up_required')->default(false)->index();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->json('attachment_paths')->nullable();
            $table->foreignId('logged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('operations_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('task_type')->index();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('agency_partner_id')->nullable()->constrained('agency_partners')->nullOnDelete();
            $table->foreignId('rate_request_id')->nullable()->constrained('rate_requests')->nullOnDelete();
            $table->foreignId('communication_id')->nullable()->constrained('communications')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority')->default('normal')->index();
            $table->string('status')->default('open')->index();
            $table->dateTime('due_at')->nullable()->index();
            $table->dateTime('original_due_at')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->dateTime('completed_at')->nullable()->index();
            $table->text('completion_notes')->nullable();
            $table->dateTime('snoozed_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('agency_partner_id')->nullable()->constrained('agency_partners')->nullOnDelete();
            $table->foreignId('rate_request_id')->nullable()->constrained('rate_requests')->nullOnDelete();
            $table->longText('body');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('document_records', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('documentable');
            $table->string('document_type')->index();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('stored_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->date('issue_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->boolean('is_confidential')->default(true)->index();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('activity_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('agency_partner_id')->nullable()->constrained('agency_partners')->nullOnDelete();
            $table->foreignId('rate_request_id')->nullable()->constrained('rate_requests')->nullOnDelete();
            $table->foreignId('communication_id')->nullable()->constrained('communications')->nullOnDelete();
            $table->foreignId('operations_task_id')->nullable()->constrained('operations_tasks')->nullOnDelete();
            $table->foreignId('internal_note_id')->nullable()->constrained('internal_notes')->nullOnDelete();
            $table->foreignId('document_record_id')->nullable()->constrained('document_records')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->dateTime('occurred_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_events');
        Schema::dropIfExists('document_records');
        Schema::dropIfExists('internal_notes');
        Schema::dropIfExists('operations_tasks');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('rate_requests');
        Schema::dropIfExists('agency_contacts');
        Schema::dropIfExists('agency_partners');
        Schema::dropIfExists('supplier_contacts');
        Schema::dropIfExists('suppliers');
    }
};
