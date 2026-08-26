<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->date('arrival_date')->nullable()->after('travel_date');
            $table->date('departure_date')->nullable()->after('arrival_date');
            $table->string('nationality')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['arrival_date', 'departure_date', 'nationality']);
        });
    }
};
