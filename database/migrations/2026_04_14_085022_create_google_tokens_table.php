<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->default('admin');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->integer('expires_in');
            $table->timestamp('token_created_at');
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_tokens');
    }
};
