<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_email_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('email_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft')->index();
            $table->date('start_date')->nullable()->index();
            $table->time('send_window_starts_at')->default('09:00');
            $table->unsignedSmallInteger('daily_limit')->default(10);
            $table->unsignedSmallInteger('interval_minutes')->default(5);
            $table->json('agency_partner_ids')->nullable();
            $table->json('agency_contact_ids')->nullable();
            $table->json('partner_collection_ids')->nullable();
            $table->longText('manual_recipients')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->string('subject_override')->nullable();
            $table->longText('body_override')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('agency_email_campaign_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('agency_email_campaigns')->cascadeOnDelete();
            $table->foreignId('agency_partner_id')->nullable()->constrained('agency_partners')->nullOnDelete();
            $table->foreignId('agency_contact_id')->nullable()->constrained('agency_contacts')->nullOnDelete();
            $table->foreignId('communication_id')->nullable()->constrained('communications')->nullOnDelete();
            $table->string('recipient_email')->nullable()->index();
            $table->string('recipient_name')->nullable();
            $table->string('status')->default('scheduled')->index();
            $table->dateTime('scheduled_for')->nullable()->index();
            $table->dateTime('sent_at')->nullable()->index();
            $table->text('failure_reason')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamps();

            $table->unique(['campaign_id', 'agency_partner_id'], 'agency_campaign_partner_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_email_campaign_recipients');
        Schema::dropIfExists('agency_email_campaigns');
    }
};
