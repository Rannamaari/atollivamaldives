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
            $table->text('quotation_payment_details')->nullable()->after('hero_image');
            $table->text('quotation_default_notes')->nullable()->after('quotation_payment_details');
            $table->decimal('quotation_service_charge_rate', 5, 2)->default(10)->after('quotation_default_notes');
            $table->decimal('quotation_tgst_rate', 5, 2)->default(17)->after('quotation_service_charge_rate');
            $table->decimal('quotation_green_tax_default_rate', 8, 2)->default(12)->after('quotation_tgst_rate');
            $table->decimal('quotation_green_tax_guesthouse_rate', 8, 2)->default(6)->after('quotation_green_tax_default_rate');
        });

        DB::table('site_settings')->update([
            'quotation_payment_details' => DB::raw("COALESCE(quotation_payment_details, 'Bank Name:\nAccount Name:\nAccount No.:\nSWIFT/BIC:')"),
            'quotation_default_notes' => DB::raw("COALESCE(quotation_default_notes, 'Rates are subject to availability until confirmed.\nQuotation validity: 7 days unless otherwise stated.')"),
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'quotation_payment_details',
                'quotation_default_notes',
                'quotation_service_charge_rate',
                'quotation_tgst_rate',
                'quotation_green_tax_default_rate',
                'quotation_green_tax_guesthouse_rate',
            ]);
        });
    }
};
