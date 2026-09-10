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
                if (!Schema::hasColumn('google_graduation_letters', 'academic_year')) {
                    $table->string('academic_year')->nullable()->after('uuid');
                }
                if (!Schema::hasColumn('google_graduation_letters', 'headmaster_id')) {
                    $table->uuid('headmaster_id')->nullable()->after('academic_year');
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
            if (Schema::hasColumn('google_graduation_letters', 'academic_year')) {
                $columnsToDrop[] = 'academic_year';
            }
            if (Schema::hasColumn('google_graduation_letters', 'headmaster_id')) {
                $columnsToDrop[] = 'headmaster_id';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
