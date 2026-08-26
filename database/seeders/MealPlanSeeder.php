<?php

namespace Database\Seeders;

use App\Models\MealPlan;
use Illuminate\Database\Seeder;

class MealPlanSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'RO', 'name' => 'Room Only', 'description' => 'Accommodation without meals included.'],
            ['code' => 'BB', 'name' => 'Bed & Breakfast', 'description' => 'Daily breakfast included.'],
            ['code' => 'HB', 'name' => 'Half Board', 'description' => 'Breakfast and one main meal included.'],
            ['code' => 'FB', 'name' => 'Full Board', 'description' => 'Breakfast, lunch, and dinner included.'],
            ['code' => 'AI', 'name' => 'All Inclusive', 'description' => 'Meals and selected drinks included.'],
            ['code' => 'PAI', 'name' => 'Premium All Inclusive', 'description' => 'Enhanced all-inclusive plan with broader inclusions.'],
        ] as $mealPlan) {
            MealPlan::updateOrCreate(['code' => $mealPlan['code']], $mealPlan);
        }
    }
}
