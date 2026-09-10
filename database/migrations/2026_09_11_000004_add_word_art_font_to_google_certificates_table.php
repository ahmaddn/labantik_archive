<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('google_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('google_certificates', 'word_art_font')) {
                $table->string('word_art_font')->default('cinzel')->after('word_art_style');
            }
        });
    }

    public function down(): void
    {
        Schema::table('google_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('google_certificates', 'word_art_font')) {
                $table->dropColumn('word_art_font');
            }
        });
    }
};
