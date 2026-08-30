<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agency_partners', function (Blueprint $table): void {
            $table->string('email')->nullable()->after('website');
        });

        DB::statement("
            update agency_partners
            set email = (
                select agency_contacts.email
                from agency_contacts
                where agency_contacts.agency_partner_id = agency_partners.id
                  and agency_contacts.email is not null
                  and agency_contacts.email <> ''
                order by agency_contacts.is_primary desc, agency_contacts.id asc
                limit 1
            )
            where (agency_partners.email is null or agency_partners.email = '')
        ");
    }

    public function down(): void
    {
        Schema::table('agency_partners', function (Blueprint $table): void {
            $table->dropColumn('email');
        });
    }
};
