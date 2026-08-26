<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable()->index();
            $table->string('last_name')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('whatsapp')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique()->after('id');
            $table->foreignId('customer_id')->nullable()->after('reference')->constrained()->nullOnDelete();
            $table->foreignId('accommodation_id')->nullable()->after('customer_id')->constrained('accommodations')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->after('accommodation_id')->constrained('rooms')->nullOnDelete();
            $table->date('check_in')->nullable()->after('departure_date');
            $table->date('check_out')->nullable()->after('check_in');
            $table->unsignedSmallInteger('number_of_nights')->nullable()->after('check_out');
            $table->unsignedSmallInteger('adults')->nullable()->after('number_of_nights');
            $table->unsignedSmallInteger('children')->nullable()->after('adults');
            $table->unsignedSmallInteger('infants')->nullable()->after('children');
            $table->string('preferred_room')->nullable()->after('room_id');
            $table->string('meal_plan')->nullable()->after('preferred_room');
            $table->foreignId('assigned_to')->nullable()->after('source')->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('accommodation_id');
            $table->dropConstrainedForeignId('room_id');
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn([
                'reference',
                'check_in',
                'check_out',
                'number_of_nights',
                'adults',
                'children',
                'infants',
                'preferred_room',
                'meal_plan',
                'notes',
            ]);
        });

        Schema::dropIfExists('customers');
    }
};
