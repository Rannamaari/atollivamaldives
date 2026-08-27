<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_collections', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('scope')->default('both')->index();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('partner_collection_supplier', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('partner_collection_id')->constrained('partner_collections')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['partner_collection_id', 'supplier_id']);
        });

        Schema::create('agency_partner_partner_collection', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('partner_collection_id')->constrained('partner_collections')->cascadeOnDelete();
            $table->foreignId('agency_partner_id')->constrained('agency_partners')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['partner_collection_id', 'agency_partner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_partner_partner_collection');
        Schema::dropIfExists('partner_collection_supplier');
        Schema::dropIfExists('partner_collections');
    }
};
