<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('liveaboard_pages', function (Blueprint $table): void {
            $table->string('arabic_eyebrow')->nullable()->after('eyebrow');
            $table->string('arabic_title')->nullable()->after('title');
            $table->text('arabic_intro')->nullable()->after('intro');
            $table->longText('arabic_body')->nullable()->after('body');
            $table->string('arabic_contact_heading')->nullable()->after('contact_heading');
            $table->text('arabic_contact_text')->nullable()->after('contact_text');
        });
    }

    public function down(): void
    {
        Schema::table('liveaboard_pages', function (Blueprint $table): void {
            $table->dropColumn([
                'arabic_eyebrow',
                'arabic_title',
                'arabic_intro',
                'arabic_body',
                'arabic_contact_heading',
                'arabic_contact_text',
            ]);
        });
    }
};
