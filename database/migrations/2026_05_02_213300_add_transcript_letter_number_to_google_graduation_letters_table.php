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
            if (!Schema::hasColumn('google_graduation_letters', 'transcript_letter_number')) {
                $table->string('transcript_letter_number')->nullable()->after('letter_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_graduation_letters', function (Blueprint $table) {
            if (Schema::hasColumn('google_graduation_letters', 'transcript_letter_number')) {
                $table->dropColumn('transcript_letter_number');
            }
        });
    }
};
