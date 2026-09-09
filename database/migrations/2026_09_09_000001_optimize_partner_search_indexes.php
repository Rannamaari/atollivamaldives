<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table): void {
            $table->index('legal_name', 'suppliers_legal_name_index');
        });

        Schema::table('agency_partners', function (Blueprint $table): void {
            $table->index('legal_company_name', 'agency_partners_legal_name_index');
        });

        Schema::table('supplier_contacts', function (Blueprint $table): void {
            $table->index('supplier_id', 'supplier_contacts_supplier_lookup_index');
        });

        Schema::table('agency_contacts', function (Blueprint $table): void {
            $table->index('agency_partner_id', 'agency_contacts_partner_lookup_index');
        });

        Schema::table('partner_collection_supplier', function (Blueprint $table): void {
            $table->index('supplier_id', 'partner_collection_supplier_lookup_index');
        });

        Schema::table('agency_partner_partner_collection', function (Blueprint $table): void {
            $table->index('agency_partner_id', 'agency_partner_collection_lookup_index');
        });

        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Built-in PostgreSQL full-text indexes make broad partner searches fast.
        DB::statement("CREATE INDEX suppliers_search_document_index ON suppliers USING gin (to_tsvector('simple', coalesce(legal_name, '') || ' ' || coalesce(trading_name, '') || ' ' || coalesce(atoll, '') || ' ' || coalesce(island, '') || ' ' || coalesce(country, '') || ' ' || coalesce(general_email, '') || ' ' || coalesce(sales_email, '') || ' ' || coalesce(reservations_email, '') || ' ' || coalesce(contracting_email, '') || ' ' || coalesce(accounts_email, '') || ' ' || coalesce(main_telephone, '') || ' ' || coalesce(whatsapp_number, '') || ' ' || coalesce(website, '')))");
        DB::statement("CREATE INDEX supplier_contacts_search_document_index ON supplier_contacts USING gin (to_tsvector('simple', coalesce(full_name, '') || ' ' || coalesce(job_title, '') || ' ' || coalesce(department, '') || ' ' || coalesce(email, '') || ' ' || coalesce(telephone, '') || ' ' || coalesce(whatsapp_number, '')))");
        DB::statement("CREATE INDEX agency_partners_search_document_index ON agency_partners USING gin (to_tsvector('simple', coalesce(legal_company_name, '') || ' ' || coalesce(trading_name, '') || ' ' || coalesce(country, '') || ' ' || coalesce(city, '') || ' ' || coalesce(email, '') || ' ' || coalesce(website, '') || ' ' || coalesce(licence_number, '') || ' ' || coalesce(target_customer_segment, '') || ' ' || coalesce(source_markets, '') || ' ' || coalesce(preferred_products, '')))");
        DB::statement("CREATE INDEX agency_contacts_search_document_index ON agency_contacts USING gin (to_tsvector('simple', coalesce(full_name, '') || ' ' || coalesce(position, '') || ' ' || coalesce(department, '') || ' ' || coalesce(email, '') || ' ' || coalesce(telephone, '') || ' ' || coalesce(whatsapp_number, '')))");
        DB::statement("CREATE INDEX partner_collections_search_document_index ON partner_collections USING gin (to_tsvector('simple', name))");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS partner_collections_search_document_index');
            DB::statement('DROP INDEX IF EXISTS agency_contacts_search_document_index');
            DB::statement('DROP INDEX IF EXISTS agency_partners_search_document_index');
            DB::statement('DROP INDEX IF EXISTS supplier_contacts_search_document_index');
            DB::statement('DROP INDEX IF EXISTS suppliers_search_document_index');
        }

        Schema::table('agency_partner_partner_collection', function (Blueprint $table): void {
            $table->dropIndex('agency_partner_collection_lookup_index');
        });

        Schema::table('partner_collection_supplier', function (Blueprint $table): void {
            $table->dropIndex('partner_collection_supplier_lookup_index');
        });

        Schema::table('agency_contacts', function (Blueprint $table): void {
            $table->dropIndex('agency_contacts_partner_lookup_index');
        });

        Schema::table('supplier_contacts', function (Blueprint $table): void {
            $table->dropIndex('supplier_contacts_supplier_lookup_index');
        });

        Schema::table('agency_partners', function (Blueprint $table): void {
            $table->dropIndex('agency_partners_legal_name_index');
        });

        Schema::table('suppliers', function (Blueprint $table): void {
            $table->dropIndex('suppliers_legal_name_index');
        });
    }
};
