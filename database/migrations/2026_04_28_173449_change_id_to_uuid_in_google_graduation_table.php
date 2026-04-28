<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            // Hapus kolom id (integer) yang lama
            $table->dropColumn('id');
        });

        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            // Tambahkan kembali kolom dengan nama 'uuid' sebagai Primary Key
            // diletakkan di posisi pertama (paling atas)
            $table->uuid('uuid')->primary()->first();
        });
    }

    public function down(): void
    {
        // Logika rollback (opsional)
    }
};
