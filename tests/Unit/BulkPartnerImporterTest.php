<?php

namespace Tests\Unit;

use App\Models\AgencyPartner;
use App\Models\Supplier;
use App\Services\OperationsHub\BulkPartnerImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkPartnerImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_suppliers_and_primary_contacts_from_csv(): void
    {
        $path = storage_path('framework/testing/suppliers-import.csv');

        file_put_contents($path, implode("\n", [
            'company_name,trading_name,supplier_type,country,atoll,island,website,general_email,sales_email,reservations_email,contracting_email,phone,whatsapp,contact_person,contact_designation,contact_email,contact_phone,notes',
            'Baros Maldives,Baros,resort,Maldives,Kaafu,Baros,https://baros.com,info@baros.com,sales@baros.com,res@baros.com,contract@baros.com,+9601234567,+9601234567,Reservations Team,Reservations,res@baros.com,+9605550000,Imported row',
        ]));

        $summary = app(BulkPartnerImporter::class)->importSuppliers($path);

        $this->assertSame(1, $summary['created']);
        $this->assertSame(0, $summary['updated']);
        $this->assertSame(1, $summary['contacts_created']);
        $this->assertEmpty($summary['errors']);
        $this->assertDatabaseHas('suppliers', [
            'legal_name' => 'Baros Maldives',
            'trading_name' => 'Baros',
            'contracting_email' => 'contract@baros.com',
        ]);
        $this->assertDatabaseHas('supplier_contacts', [
            'full_name' => 'Reservations Team',
            'email' => 'res@baros.com',
            'is_primary' => true,
        ]);
    }

    public function test_it_imports_agency_partners_and_primary_contacts_from_csv(): void
    {
        $path = storage_path('framework/testing/agency-import.csv');

        file_put_contents($path, implode("\n", [
            'company_name,trading_name,country,city,website,contact_person,contact_designation,contact_email,contact_phone,whatsapp,target_customer_segment,source_markets,preferred_products,notes',
            'Blue Ocean Travel LLC,Blue Ocean Travel,United Arab Emirates,Dubai,https://blueocean.example,Jane Smith,Product Manager,jane@example.com,+971500000000,+971500000000,Luxury travellers,"UAE, GCC","Resorts, Liveaboards",Imported row',
        ]));

        $summary = app(BulkPartnerImporter::class)->importAgencyPartners($path);

        $this->assertSame(1, $summary['created']);
        $this->assertSame(0, $summary['updated']);
        $this->assertSame(1, $summary['contacts_created']);
        $this->assertEmpty($summary['errors']);
        $this->assertDatabaseHas('agency_partners', [
            'legal_company_name' => 'Blue Ocean Travel LLC',
            'trading_name' => 'Blue Ocean Travel',
            'country' => 'United Arab Emirates',
        ]);
        $this->assertDatabaseHas('agency_contacts', [
            'full_name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'is_primary' => true,
        ]);
    }
}
