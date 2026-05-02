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
            $table->decimal('sem_1', 8, 2)->nullable()->after('mapel_id');
            $table->decimal('sem_2', 8, 2)->nullable()->after('sem_1');
            $table->decimal('sem_3', 8, 2)->nullable()->after('sem_2');
            $table->decimal('sem_4', 8, 2)->nullable()->after('sem_3');
            $table->decimal('sem_5', 8, 2)->nullable()->after('sem_4');
            $table->decimal('sem_6', 8, 2)->nullable()->after('sem_5');
            $table->decimal('nr', 8, 2)->nullable()->after('sem_6');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->dropColumn(['sem_1', 'sem_2', 'sem_3', 'sem_4', 'sem_5', 'sem_6', 'nr']);
        });
    }
};
