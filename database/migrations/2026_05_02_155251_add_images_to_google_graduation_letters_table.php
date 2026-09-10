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
        if (Schema::hasTable('google_graduation_letters')) {
            Schema::table('google_graduation_letters', function (Blueprint $table) {
                if (!Schema::hasColumn('google_graduation_letters', 'stamp_image')) {
                    $table->string('stamp_image')->nullable()->after('transcript_letter_number');
                }
                if (!Schema::hasColumn('google_graduation_letters', 'signature_image')) {
                    $table->string('signature_image')->nullable()->after('stamp_image');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_graduation_letters', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('google_graduation_letters', 'stamp_image')) {
                $columnsToDrop[] = 'stamp_image';
            }
            if (Schema::hasColumn('google_graduation_letters', 'signature_image')) {
                $columnsToDrop[] = 'signature_image';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
