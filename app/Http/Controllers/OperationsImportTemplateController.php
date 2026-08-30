<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class OperationsImportTemplateController extends Controller
{
    public function __invoke(string $type): Response
    {
        $templates = [
            'suppliers' => [
                ['company_name', 'trading_name', 'supplier_type', 'country', 'atoll', 'island', 'website', 'general_email', 'sales_email', 'reservations_email', 'contracting_email', 'phone', 'whatsapp', 'contact_person', 'contact_designation', 'contact_email', 'contact_phone', 'notes'],
                ['Baros Maldives', 'Baros', 'resort', 'Maldives', 'Kaafu', 'Baros', 'https://www.baros.com', 'info@example.com', 'sales@example.com', 'reservations@example.com', 'contracting@example.com', '+9600000000', '+9600000000', 'Reservations Team', 'Reservations', 'reservations@example.com', '+9600000001', 'Optional notes'],
            ],
            'agency-partners' => [
                ['company_name', 'trading_name', 'country', 'city', 'website', 'email', 'contact_person', 'contact_designation', 'contact_email', 'contact_phone', 'whatsapp', 'target_customer_segment', 'source_markets', 'preferred_products', 'notes'],
                ['Blue Ocean Travel LLC', 'Blue Ocean Travel', 'United Arab Emirates', 'Dubai', 'https://example.com', 'contracting@example.com', 'Jane Smith', 'Product Manager', 'jane@example.com', '+971500000000', '+971500000000', 'Luxury leisure travellers', 'UAE, GCC', 'Resorts, Liveaboards', 'Optional notes'],
            ],
        ];

        abort_unless(array_key_exists($type, $templates), 404);

        $handle = fopen('php://temp', 'r+');

        foreach ($templates[$type] as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$type.'-import-template.csv"',
        ]);
    }
}
