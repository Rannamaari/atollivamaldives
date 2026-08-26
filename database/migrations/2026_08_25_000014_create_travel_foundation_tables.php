<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atolls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable()->index();
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atoll_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::table('accommodations', function (Blueprint $table) {
            $table->foreignId('atoll_id')->nullable()->after('atoll')->constrained()->nullOnDelete();
            $table->foreignId('island_id')->nullable()->after('island')->constrained()->nullOnDelete();
            $table->string('city')->nullable()->after('island_id');
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('status')->nullable()->after('type')->index();
            $table->string('featured_image')->nullable()->after('images');
            $table->time('check_in_time')->nullable()->after('featured');
            $table->time('check_out_time')->nullable()->after('check_in_time');
            $table->string('airport_distance')->nullable()->after('check_out_time');
            $table->string('transfer_duration')->nullable()->after('airport_distance');
            $table->text('transfer_notes')->nullable()->after('transfer_duration');
            $table->longText('house_rules')->nullable()->after('transfer_notes');
            $table->longText('cancellation_policy')->nullable()->after('house_rules');
        });

        DB::table('accommodations')->where('published', true)->update(['status' => 'published']);
        DB::table('accommodations')->where('published', false)->update(['status' => 'draft']);

        Schema::create('accommodation_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('category')->nullable()->index();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('accommodation_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->unique(['accommodation_id', 'facility_id']);
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('room_size')->nullable();
            $table->string('bed_type')->nullable();
            $table->unsignedSmallInteger('max_adults')->nullable();
            $table->unsignedSmallInteger('max_children')->nullable();
            $table->unsignedSmallInteger('max_occupancy')->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->char('currency', 3)->default('USD');
            $table->boolean('featured')->default(false)->index();
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('room_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->unique(['room_id', 'facility_id']);
        });

        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('property_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->string('transfer_type')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('adult_price', 12, 2)->nullable();
            $table->decimal('child_price', 12, 2)->nullable();
            $table->decimal('infant_price', 12, 2)->nullable();
            $table->char('currency', 3)->default('USD');
            $table->boolean('mandatory')->default(false);
            $table->timestamps();
        });

        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable()->index();
            $table->decimal('value', 12, 2);
            $table->string('value_type');
            $table->string('applicable_to')->nullable()->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
        Schema::dropIfExists('property_transfers');
        Schema::dropIfExists('meal_plans');
        Schema::dropIfExists('room_facility');
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('accommodation_facility');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('accommodation_images');

        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('atoll_id');
            $table->dropConstrainedForeignId('island_id');
            $table->dropColumn([
                'city',
                'latitude',
                'longitude',
                'status',
                'featured_image',
                'check_in_time',
                'check_out_time',
                'airport_distance',
                'transfer_duration',
                'transfer_notes',
                'house_rules',
                'cancellation_policy',
            ]);
        });

        Schema::dropIfExists('islands');
        Schema::dropIfExists('atolls');
    }
};
