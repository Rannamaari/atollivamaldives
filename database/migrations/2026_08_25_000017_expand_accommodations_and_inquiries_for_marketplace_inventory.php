<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->string('previous_name')->nullable()->after('name');
            $table->json('aliases')->nullable()->after('previous_name');
            $table->string('property_subtype')->nullable()->after('type');
            $table->string('country')->nullable()->after('city');
            $table->string('official_website')->nullable()->after('address');
            $table->string('source_url')->nullable()->after('official_website');
            $table->boolean('verified')->default(false)->after('featured')->index();
            $table->string('vessel_name')->nullable()->after('verified');
            $table->string('vessel_type')->nullable()->after('vessel_name');
            $table->unsignedSmallInteger('cabins')->nullable()->after('vessel_type');
            $table->unsignedSmallInteger('maximum_guests')->nullable()->after('cabins');
            $table->decimal('length_meters', 6, 2)->nullable()->after('maximum_guests');
            $table->decimal('cruising_speed_knots', 5, 2)->nullable()->after('length_meters');
            $table->boolean('diving_available')->nullable()->after('cruising_speed_knots');
            $table->boolean('surfing_available')->nullable()->after('diving_available');
            $table->boolean('snorkeling_available')->nullable()->after('surfing_available');
            $table->boolean('nitrox_available')->nullable()->after('snorkeling_available');
            $table->boolean('dhoni_available')->nullable()->after('nitrox_available');
            $table->boolean('jacuzzi')->nullable()->after('dhoni_available');
            $table->boolean('spa')->nullable()->after('jacuzzi');
            $table->boolean('restaurant')->nullable()->after('spa');
            $table->boolean('bar')->nullable()->after('restaurant');
            $table->boolean('wifi')->nullable()->after('bar');
            $table->string('departure_port')->nullable()->after('wifi');
            $table->text('typical_route')->nullable()->after('departure_port');
            $table->string('typical_trip_length')->nullable()->after('typical_route');
            $table->unsignedSmallInteger('minimum_nights')->nullable()->after('typical_trip_length');
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->text('children_ages')->nullable()->after('children');
            $table->string('preferred_atoll')->nullable()->after('budget');
            $table->string('transfer_preference')->nullable()->after('preferred_atoll');
            $table->boolean('honeymoon')->default(false)->after('transfer_preference');
            $table->boolean('family_trip')->default(false)->after('honeymoon');
            $table->boolean('diving_trip')->default(false)->after('family_trip');
            $table->boolean('surfing_trip')->default(false)->after('diving_trip');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'children_ages',
                'preferred_atoll',
                'transfer_preference',
                'honeymoon',
                'family_trip',
                'diving_trip',
                'surfing_trip',
            ]);
        });

        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn([
                'previous_name',
                'aliases',
                'property_subtype',
                'country',
                'official_website',
                'source_url',
                'verified',
                'vessel_name',
                'vessel_type',
                'cabins',
                'maximum_guests',
                'length_meters',
                'cruising_speed_knots',
                'diving_available',
                'surfing_available',
                'snorkeling_available',
                'nitrox_available',
                'dhoni_available',
                'jacuzzi',
                'spa',
                'restaurant',
                'bar',
                'wifi',
                'departure_port',
                'typical_route',
                'typical_trip_length',
                'minimum_nights',
            ]);
        });
    }
};
