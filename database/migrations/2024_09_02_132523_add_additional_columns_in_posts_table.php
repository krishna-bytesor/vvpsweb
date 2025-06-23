<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->text('content_2')->nullable()->after('content');
            $table->string('language')->nullable()->after('content_2')->default('en');
            $table->string('festival')->nullable()->after('language');
            $table->string('shloka_part')->nullable();
            $table->string('shloka_chapter')->nullable();

            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'content_2',
                'language',
                'festival',
                'shloka_part',
                'shloka_chapter',
                'type'
            ]);
        });
    }
};
