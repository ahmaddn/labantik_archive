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
        Schema::table('google_graduation', function (Blueprint $table) {
            $table->uuid('transcript_letter_id')->nullable()->after('letter_id');
            $table->foreign('transcript_letter_id')->references('uuid')->on('google_graduation_letters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_graduation', function (Blueprint $table) {
            $table->dropForeign(['transcript_letter_id']);
            $table->dropColumn('transcript_letter_id');
        });
    }
};
