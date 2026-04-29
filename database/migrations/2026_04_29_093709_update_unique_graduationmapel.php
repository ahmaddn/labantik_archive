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
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->dropUnique(['graduation_id']);
            $table->unique(['graduation_id', 'mapel_id']); // composite unique
        });
    }

    public function down(): void
    {
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->dropUnique(['graduation_id', 'mapel_id']);
            $table->unique(['graduation_id']);
        });
    }
};
