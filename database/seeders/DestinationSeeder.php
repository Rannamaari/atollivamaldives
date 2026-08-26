<?php

namespace Database\Seeders;

use App\Models\Atoll;
use App\Models\Island;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $atolls = [
            'Kaafu' => ['code' => 'K', 'status' => 'published'],
            'Alif Alif' => ['code' => 'AA', 'status' => 'published'],
            'Alif Dhaal' => ['code' => 'ADh', 'status' => 'published'],
            'Baa' => ['code' => 'B', 'status' => 'published'],
            'Noonu' => ['code' => 'N', 'status' => 'published'],
            'Vaavu' => ['code' => 'V', 'status' => 'published'],
        ];

        foreach ($atolls as $name => $data) {
            Atoll::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name] + $data
            );
        }

        $islands = [
            ['name' => 'Maafushi', 'atoll' => 'Kaafu'],
            ['name' => 'Dhigurah', 'atoll' => 'Alif Dhaal'],
            ['name' => 'Thulusdhoo', 'atoll' => 'Kaafu'],
            ['name' => 'Ukulhas', 'atoll' => 'Alif Alif'],
            ['name' => 'Rasdhoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Thinadhoo', 'atoll' => 'Vaavu'],
            ['name' => 'Dhiffushi', 'atoll' => 'Kaafu'],
            ['name' => 'Thoddoo', 'atoll' => 'Alif Alif'],
            ['name' => 'Malé', 'atoll' => null],
            ['name' => 'Hulhulé', 'atoll' => null],
        ];

        foreach ($islands as $island) {
            $atoll = $island['atoll']
                ? Atoll::query()->where('name', $island['atoll'])->first()
                : null;

            Island::updateOrCreate(
                ['slug' => Str::slug($island['name'])],
                [
                    'name' => $island['name'],
                    'atoll_id' => $atoll?->id,
                    'status' => 'published',
                ]
            );
        }
    }
}
