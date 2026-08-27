<?php

namespace Database\Factories;

use App\Enums\SupplierPartnershipStatus;
use App\Enums\SupplierType;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'legal_name' => fake()->company(),
            'trading_name' => fake()->company().' Maldives',
            'supplier_type' => SupplierType::Resort,
            'country' => 'Maldives',
            'partnership_status' => SupplierPartnershipStatus::NotContacted,
            'is_active' => true,
        ];
    }
}
