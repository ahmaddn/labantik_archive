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
        if (!Schema::hasTable('google_certificate_users')) {
            Schema::create('google_certificate_users', function (Blueprint $table) {
                $table->uuid('certificate_id');
                $table->uuid('user_id');

                $table->primary(['certificate_id', 'user_id']);

                $table->foreign('certificate_id')
                    ->references('id')
                    ->on('google_certificates')
                    ->onDelete('cascade');

                if (Schema::hasTable('core_users')) {
                    $table->foreign('user_id')
                        ->references('id')
                        ->on('core_users')
                        ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_certificate_users');
    }
};
