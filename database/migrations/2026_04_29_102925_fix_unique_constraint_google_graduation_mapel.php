<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            // Drop constraint lama
            $table->dropUnique('google_graduation_mapel_mapel_id_unique');

            // Buat constraint baru yang benar
            $table->unique(['graduation_id', 'mapel_id'], 'graduation_mapel_unique');
        });
    }

    public function down()
    {
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->dropUnique('graduation_mapel_unique');
            $table->unique('mapel_id');
        });
    }
};
