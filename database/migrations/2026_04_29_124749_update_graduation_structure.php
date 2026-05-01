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

        // Hapus order & join dari graduation_mapel (jika sudah ada)
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('join');
        });

        // Tambah order & join ke google_mapel
        Schema::table('google_mapel', function (Blueprint $table) {
            $table->integer('order')->default(999)->after('type');
            $table->integer('join')->default(0)->after('order');
        });
    }

    public function down(): void
    {

        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->integer('order')->default(1)->after('score');
            $table->integer('join')->default(1)->after('order');
        });

        Schema::table('google_mapel', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('join');
        });
    }
};
