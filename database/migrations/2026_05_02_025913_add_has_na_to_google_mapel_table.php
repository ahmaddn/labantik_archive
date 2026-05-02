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
        Schema::table('google_mapel', function (Blueprint $bubble) {
            $bubble->boolean('has_na')->default(true)->after('join');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_mapel', function (Blueprint $bubble) {
            $bubble->dropColumn('has_na');
        });
    }
};
