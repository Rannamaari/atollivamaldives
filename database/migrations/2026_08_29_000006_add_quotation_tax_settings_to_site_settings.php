<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('quotation_tax_settings')->nullable()->after('quotation_terms');
        });

        $defaults = json_encode([
            [
                'name' => 'Service Charge',
                'type' => 'percentage_of_subtotal',
                'rate_default' => 10,
                'rate_guesthouse' => 10,
                'active' => true,
            ],
            [
                'name' => 'TGST',
                'type' => 'percentage_of_subtotal',
                'rate_default' => 17,
                'rate_guesthouse' => 17,
                'active' => true,
            ],
            [
                'name' => 'Green Tax',
                'type' => 'per_person_per_night',
                'rate_default' => 12,
                'rate_guesthouse' => 6,
                'active' => true,
            ],
        ]);

        DB::table('site_settings')->update([
            'quotation_tax_settings' => DB::raw("COALESCE(quotation_tax_settings, '{$defaults}')"),
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('quotation_tax_settings');
        });
    }
};
