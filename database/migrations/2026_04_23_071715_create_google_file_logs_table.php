<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_file_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('google_drive_file_id');
            $table->uuid('google_drive_sub_category_id');
            $table->string('sub_category_option')->nullable(); // nilai pilihan yang dipilih user
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->foreign('google_drive_file_id')
                ->references('id')->on('google_drive_files')
                ->onDelete('cascade');

            $table->foreign('google_drive_sub_category_id')
                ->references('id')->on('google_drive_sub_categories')
                ->onDelete('cascade');

            $table->index('google_drive_file_id', 'file_logs_file_idx');
            $table->index('google_drive_sub_category_id', 'file_logs_sub_cat_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_file_logs');
    }
};
