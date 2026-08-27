<?php

namespace App\Services\OperationsHub;

use App\Enums\AgencyPartnershipStatus;
use App\Enums\AgencyRiskLevel;
use App\Enums\SupplierPartnershipStatus;
use App\Enums\SupplierType;
use App\Models\AgencyContact;
use App\Models\AgencyPartner;
use App\Models\Supplier;
use App\Models\SupplierContact;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Throwable;

class BulkPartnerImporter
{
    public function importSuppliers(UploadedFile|string $file): array
    {
        $rows = $this->csvRows($file);
        $summary = ['created' => 0, 'updated' => 0, 'contacts_created' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                DB::transaction(function () use ($row, &$summary): void {
                    $legalName = trim((string) ($row['company_name'] ?? ''));

                    if ($legalName === '') {
                        throw new \RuntimeException('Company name is required.');
                    }

                    $supplier = Supplier::query()->firstOrNew([
                        'legal_name' => $legalName,
                        'country' => trim((string) ($row['country'] ?? 'Maldives')) ?: 'Maldives',
                    ]);

                    $isExisting = $supplier->exists;

                    $supplier->fill([
                        'trading_name' => $this->nullable($row['trading_name'] ?? null),
                        'supplier_type' => $this->resolveSupplierType($row['supplier_type'] ?? null)->value,
                        'atoll' => $this->nullable($row['atoll'] ?? null),
                        'island' => $this->nullable($row['island'] ?? null),
                        'website' => $this->nullable($row['website'] ?? null),
                        'general_email' => $this->nullable($row['general_email'] ?? null),
                        'sales_email' => $this->nullable($row['sales_email'] ?? null),
                        'reservations_email' => $this->nullable($row['reservations_email'] ?? null),
                        'contracting_email' => $this->nullable($row['contracting_email'] ?? null),
                        'main_telephone' => $this->nullable($row['phone'] ?? null),
                        'whatsapp_number' => $this->nullable($row['whatsapp'] ?? null),
                        'internal_notes' => $this->nullable($row['notes'] ?? null),
                        'partnership_status' => SupplierPartnershipStatus::NotContacted->value,
                        'is_active' => true,
                    ]);

                    $supplier->save();
                    $summary[$isExisting ? 'updated' : 'created']++;

                    if (filled($row['contact_person'] ?? null) || filled($row['contact_email'] ?? null)) {
                        $contact = SupplierContact::query()->firstOrNew([
                            'supplier_id' => $supplier->id,
                            'email' => $this->nullable($row['contact_email'] ?? null),
                            'full_name' => trim((string) ($row['contact_person'] ?? 'Reservations Team')) ?: 'Reservations Team',
                        ]);

                        $contact->fill([
                            'job_title' => $this->nullable($row['contact_designation'] ?? null),
                            'telephone' => $this->nullable($row['contact_phone'] ?? null),
                            'whatsapp_number' => $this->nullable($row['whatsapp'] ?? null),
                            'is_primary' => true,
                            'is_active' => true,
                        ]);

                        $wasNewContact = ! $contact->exists;
                        $contact->save();

                        if ($wasNewContact) {
                            $summary['contacts_created']++;
                        }
                    }
                });
            } catch (Throwable $e) {
                $summary['errors'][] = 'Row '.($index + 2).': '.$e->getMessage();
            }
        }

        return $summary;
    }

    public function importAgencyPartners(UploadedFile|string $file): array
    {
        $rows = $this->csvRows($file);
        $summary = ['created' => 0, 'updated' => 0, 'contacts_created' => 0, 'errors' => []];

        foreach ($rows as $index => $row) {
            try {
                DB::transaction(function () use ($row, &$summary): void {
                    $legalCompanyName = trim((string) ($row['company_name'] ?? ''));

                    if ($legalCompanyName === '') {
                        throw new \RuntimeException('Company name is required.');
                    }

                    $agency = AgencyPartner::query()->firstOrNew([
                        'legal_company_name' => $legalCompanyName,
                        'country' => trim((string) ($row['country'] ?? '')) ?: 'Maldives',
                    ]);

                    $isExisting = $agency->exists;

                    $agency->fill([
                        'trading_name' => $this->nullable($row['trading_name'] ?? null),
                        'city' => $this->nullable($row['city'] ?? null),
                        'website' => $this->nullable($row['website'] ?? null),
                        'target_customer_segment' => $this->nullable($row['target_customer_segment'] ?? null),
                        'source_markets' => $this->nullable($row['source_markets'] ?? null),
                        'preferred_products' => $this->nullable($row['preferred_products'] ?? null),
                        'internal_notes' => $this->nullable($row['notes'] ?? null),
                        'partnership_status' => AgencyPartnershipStatus::ProspectIdentified->value,
                        'risk_level' => AgencyRiskLevel::NotAssessed->value,
                        'is_active' => true,
                    ]);

                    $agency->save();
                    $summary[$isExisting ? 'updated' : 'created']++;

                    if (filled($row['contact_person'] ?? null) || filled($row['contact_email'] ?? null)) {
                        $contact = AgencyContact::query()->firstOrNew([
                            'agency_partner_id' => $agency->id,
                            'email' => $this->nullable($row['contact_email'] ?? null),
                            'full_name' => trim((string) ($row['contact_person'] ?? 'Partnership Team')) ?: 'Partnership Team',
                        ]);

                        $contact->fill([
                            'position' => $this->nullable($row['contact_designation'] ?? null),
                            'telephone' => $this->nullable($row['contact_phone'] ?? null),
                            'whatsapp_number' => $this->nullable($row['whatsapp'] ?? null),
                            'is_primary' => true,
                            'is_active' => true,
                        ]);

                        $wasNewContact = ! $contact->exists;
                        $contact->save();

                        if ($wasNewContact) {
                            $summary['contacts_created']++;
                        }
                    }
                });
            } catch (Throwable $e) {
                $summary['errors'][] = 'Row '.($index + 2).': '.$e->getMessage();
            }
        }

        return $summary;
    }

    protected function csvRows(UploadedFile|string $file): array
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! $path) {
            throw new \RuntimeException('Upload could not be read.');
        }

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        return iterator_to_array($csv->getRecords());
    }

    protected function nullable(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : $value;

        return filled($value) ? (string) $value : null;
    }

    protected function resolveSupplierType(mixed $value): SupplierType
    {
        $normalized = str((string) $value)->trim()->lower()->replace([' ', '-'], '_')->toString();

        return SupplierType::tryFrom($normalized) ?? SupplierType::Other;
    }
}
