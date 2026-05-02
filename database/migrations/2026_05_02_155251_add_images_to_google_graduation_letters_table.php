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
        Schema::table('google_graduation_letters', function (Blueprint $table) {
            $table->string('stamp_image')->nullable()->after('transcript_letter_number');
            $table->string('signature_image')->nullable()->after('stamp_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_graduation_letters', function (Blueprint $table) {
            $table->dropColumn(['stamp_image', 'signature_image']);
        });
    }
};
