<?php

namespace Database\Factories;

use App\Enums\AgencyPartnershipStatus;
use App\Enums\AgencyRiskLevel;
use App\Models\AgencyPartner;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgencyPartnerFactory extends Factory
{
    protected $model = AgencyPartner::class;

    public function definition(): array
    {
        return [
            'legal_company_name' => fake()->company(),
            'trading_name' => fake()->company(),
            'country' => fake()->country(),
            'partnership_status' => AgencyPartnershipStatus::ProspectIdentified,
            'risk_level' => AgencyRiskLevel::NotAssessed,
            'is_active' => true,
        ];
    }
}
