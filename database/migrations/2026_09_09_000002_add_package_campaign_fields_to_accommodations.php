<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->unsignedSmallInteger('package_included_adults')->nullable()->after('package_price_periods');
            $table->unsignedSmallInteger('package_included_children')->nullable()->after('package_included_adults');
            $table->boolean('package_children_free')->default(false)->after('package_included_children');
            $table->boolean('package_best_seller')->default(false)->after('package_children_free');
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table): void {
            $table->dropColumn([
                'package_included_adults',
                'package_included_children',
                'package_children_free',
                'package_best_seller',
            ]);
        });
    }
};
