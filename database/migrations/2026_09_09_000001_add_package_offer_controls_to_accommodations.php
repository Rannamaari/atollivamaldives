<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->date('offer_starts_on')->nullable()->after('price_unit');
            $table->date('offer_ends_on')->nullable()->after('offer_starts_on')->index();
            $table->json('eligible_audiences')->nullable()->after('offer_ends_on');
            $table->json('package_price_periods')->nullable()->after('eligible_audiences');
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->dropColumn([
                'offer_starts_on',
                'offer_ends_on',
                'eligible_audiences',
                'package_price_periods',
            ]);
        });
    }
};
