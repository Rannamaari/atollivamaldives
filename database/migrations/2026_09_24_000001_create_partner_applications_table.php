<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_applications', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->default('new')->index();
            $table->string('company');
            $table->string('contact_name');
            $table->string('country')->index();
            $table->string('email')->index();
            $table->string('phone', 60);
            $table->string('website')->nullable();
            $table->string('business_type')->nullable()->index();
            $table->text('markets')->nullable();
            $table->string('estimated_enquiries')->nullable();
            $table->text('message')->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_applications');
    }
};
