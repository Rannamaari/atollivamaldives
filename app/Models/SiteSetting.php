<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'hero_image',
        'site_name',
        'default_meta_title',
        'default_meta_description',
        'default_og_image',
        'business_logo',
        'business_email',
        'business_phone',
        'business_secondary_phone',
        'business_address',
        'company_description',
        'facebook_url',
        'instagram_url',
        'x_url',
        'tiktok_url',
        'default_robots_index',
        'default_robots_follow',
        'google_analytics_id',
        'google_tag_manager_id',
        'google_search_console_verification',
        'quotation_payment_details',
        'quotation_default_notes',
        'quotation_terms',
        'quotation_tax_settings',
        'quotation_service_charge_rate',
        'quotation_tgst_rate',
        'quotation_green_tax_default_rate',
        'quotation_green_tax_guesthouse_rate',
    ];

    protected function casts(): array
    {
        return [
            'quotation_tax_settings' => 'array',
            'default_robots_index' => 'boolean',
            'default_robots_follow' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public function getHeroImageUrlAttribute(): string
    {
        return $this->hero_image
            ? $this->storageImageUrl($this->hero_image)
            : 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=2200&q=90';
    }

    public function getDefaultOgImageUrlAttribute(): string
    {
        return $this->default_og_image
            ? $this->storageImageUrl($this->default_og_image)
            : asset('logo/optimized/atolliva-share.png');
    }

    public function getBusinessLogoUrlAttribute(): string
    {
        return $this->business_logo
            ? $this->storageImageUrl($this->business_logo)
            : asset('logo/optimized/atolliva-logo.png');
    }

    public function getBusinessAddressLinesAttribute(): array
    {
        return array_values(array_filter(preg_split('/\r\n|\r|\n/', (string) $this->business_address)));
    }

    public function quotationGreenTaxRateFor(?string $travelType): float
    {
        return in_array($travelType, ['guesthouse', 'city_hotel'], true)
            ? (float) $this->quotation_green_tax_guesthouse_rate
            : (float) $this->quotation_green_tax_default_rate;
    }

    public function quotationTaxSettingsFor(?string $travelType): array
    {
        $guesthouseLike = in_array($travelType, ['guesthouse', 'city_hotel'], true);
        $settings = collect($this->quotation_tax_settings ?: $this->legacyQuotationTaxSettings())
            ->filter(fn ($tax) => (bool) ($tax['active'] ?? true))
            ->values()
            ->map(function (array $tax) use ($guesthouseLike): array {
                return [
                    'name' => trim((string) ($tax['name'] ?? '')),
                    'type' => (string) ($tax['type'] ?? 'fixed'),
                    'rate' => (float) ($guesthouseLike
                        ? ($tax['rate_guesthouse'] ?? $tax['rate_default'] ?? 0)
                        : ($tax['rate_default'] ?? 0)),
                ];
            })
            ->filter(fn (array $tax) => filled($tax['name']))
            ->all();

        return blank($settings) ? $this->legacyQuotationTaxSettings() : $settings;
    }

    protected function legacyQuotationTaxSettings(): array
    {
        return [
            [
                'name' => 'Service Charge',
                'type' => 'percentage_of_subtotal',
                'rate' => (float) ($this->quotation_service_charge_rate ?? 10),
            ],
            [
                'name' => 'TGST',
                'type' => 'percentage_of_subtotal',
                'rate' => (float) ($this->quotation_tgst_rate ?? 17),
            ],
            [
                'name' => 'Green Tax',
                'type' => 'per_person_per_night',
                'rate' => (float) ($this->quotation_green_tax_default_rate ?? 12),
            ],
        ];
    }

    protected function storageImageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return str_starts_with((string) $path, 'http')
            ? (string) $path
            : asset('storage/'.ltrim((string) $path, '/'));
    }
}
