<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('company_name')->nullable()->after('last_name');
            $table->text('address')->nullable()->after('country');
            $table->string('passport_number')->nullable()->index()->after('address');
            $table->string('work_permit_number')->nullable()->index()->after('passport_number');
            $table->string('national_id_number')->nullable()->index()->after('work_permit_number');
            $table->json('dependents')->nullable()->after('national_id_number');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn([
                'company_name',
                'address',
                'passport_number',
                'work_permit_number',
                'national_id_number',
                'dependents',
            ]);
        });
    }
};
