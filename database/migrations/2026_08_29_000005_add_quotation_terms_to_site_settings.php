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
            $table->text('quotation_terms')->nullable()->after('quotation_default_notes');
        });

        DB::table('site_settings')->update([
            'quotation_terms' => DB::raw("COALESCE(quotation_terms, 'All rates are subject to availability at the time of confirmation.\nQuoted prices are based on the details listed in this quotation and may change if the travel dates, guest count, or inclusions change.\nA booking is considered confirmed only after written confirmation from Atolliva Maldives and receipt of the required payment.\nCancellation terms, amendment charges, and supplier-specific conditions may apply depending on the selected property or service.\nTransfers, excursions, and other third-party services are subject to weather, operational conditions, and supplier policies.\nPassport, visa, insurance, and travel document requirements remain the responsibility of the traveller.')"),
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn('quotation_terms');
        });
    }
};
