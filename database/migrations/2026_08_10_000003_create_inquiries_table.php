<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void { Schema::create('inquiries',function(Blueprint $table){$table->id();$table->string('name');$table->string('email')->nullable();$table->string('phone');$table->string('travel_type')->nullable();$table->date('travel_date')->nullable();$table->unsignedSmallInteger('travellers')->nullable();$table->string('budget')->nullable();$table->text('message')->nullable();$table->string('status')->default('new')->index();$table->string('source')->default('website');$table->timestamps();}); } public function down():void {Schema::dropIfExists('inquiries');} };
