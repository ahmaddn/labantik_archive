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
            $table->uuid('letter_id')->nullable()->after('uuid');
            $table->dropColumn('letter_number');
            $table->dropColumn('graduation_date');
        });

        Schema::table('google_graduation_mapel', function (Blueprint $table) {
            $table->integer('order')->default(1)->after('score');
            $table->integer('join')->default(1)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
