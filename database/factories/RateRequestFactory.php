<?php

namespace Database\Factories;

use App\Enums\RateRequestStatus;
use App\Models\RateRequest;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class RateRequestFactory extends Factory
{
    protected $model = RateRequest::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'request_title' => '2027 rates request',
            'status' => RateRequestStatus::Draft,
        ];
    }
}
