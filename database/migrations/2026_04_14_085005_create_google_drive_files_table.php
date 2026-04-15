<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_drive_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // Foreign key ke core_users
            $table->string('google_file_id')->unique();
            $table->string('name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->text('web_view_link');
            $table->text('web_content_link')->nullable();
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('core_users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_drive_files');
    }
};
