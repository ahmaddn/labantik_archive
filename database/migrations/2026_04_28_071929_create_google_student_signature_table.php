<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_student_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique(); // 1 siswa 1 tanda tangan
            $table->longText('signature_data'); // base64 PNG
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('core_users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_signatures');
    }
};
