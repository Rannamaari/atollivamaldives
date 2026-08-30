<?php

namespace App\Services;

use App\Models\SiteSetting;

class QuotationCalculator
{
    public function prepare(array $data): array
    {
        $nights = max(1, (int) ($data['nights'] ?? 1));
        $adults = max(0, (int) ($data['adults'] ?? 0));
        $children = max(0, (int) ($data['children'] ?? 0));
        $infants = max(0, (int) ($data['infants'] ?? 0));
        $chargeablePax = max(1, (int) ($data['chargeable_pax'] ?? ($adults + $children)));

        $items = collect($data['items'] ?? [])
            ->filter(fn ($item) => filled($item['description'] ?? null))
            ->values()
            ->map(function (array $item): array {
                $qty = max(1, (float) ($item['qty'] ?? 1));
                $unitPrice = round((float) ($item['unit_price'] ?? 0), 2);

                return [
                    'description' => trim((string) ($item['description'] ?? '')),
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'amount' => round($qty * $unitPrice, 2),
                ];
            })
            ->all();

        $subtotal = round(collect($items)->sum('amount'), 2);

        $taxes = collect($data['taxes'] ?? [])
            ->filter(fn ($tax) => filled($tax['name'] ?? null))
            ->values()
            ->map(function (array $tax) use ($subtotal, $nights, $chargeablePax): array {
                $type = (string) ($tax['type'] ?? 'fixed');
                $rate = round((float) ($tax['rate'] ?? 0), 2);
                $total = match ($type) {
                    'percentage_of_subtotal' => round($subtotal * ($rate / 100), 2),
                    'per_person_per_night' => round($chargeablePax * $nights * $rate, 2),
                    'per_person_once' => round($chargeablePax * $rate, 2),
                    default => round($rate, 2),
                };

                return [
                    'name' => trim((string) ($tax['name'] ?? '')),
                    'type' => $type,
                    'rate' => $rate,
                    'total' => $total,
                ];
            })
            ->all();

        $taxTotal = round(collect($taxes)->sum('total'), 2);

        return array_merge($data, [
            'nights' => $nights,
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants,
            'chargeable_pax' => $chargeablePax,
            'items' => $items,
            'taxes' => $taxes,
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'grand_total' => round($subtotal + $taxTotal, 2),
        ]);
    }

    public function defaultTaxes(?string $travelType): array
    {
        return SiteSetting::current()->quotationTaxSettingsFor($travelType);
    }

    public function greenTaxRate(?string $travelType): float
    {
        return SiteSetting::current()->quotationGreenTaxRateFor($travelType);
    }
}
