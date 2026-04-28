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
        Schema::create('google_graduation', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->char('user_id', 36)->unique();
            $table->char('mapel_id', 36);
            $table->string('letter_number');
            $table->date('graduation_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_graduation');
    }
};
