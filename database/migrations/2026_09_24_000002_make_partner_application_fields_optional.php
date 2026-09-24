<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_applications', function (Blueprint $table): void {
            $table->string('business_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('partner_applications', function (Blueprint $table): void {
            $table->string('business_type')->nullable(false)->change();
        });
    }
};
