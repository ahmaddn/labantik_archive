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
        Schema::table('google_drive_files', function (Blueprint $table) {
            $table->uuid('google_drive_sub_category_id')->nullable();
            $table->year('year')->nullable();

            // Indexes
            $table->foreign('google_drive_sub_category_id')
                ->references('id')
                ->on('google_drive_sub_categories')
                ->onDelete('set null');
            $table->index('google_drive_sub_category_id');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_drive_files', function (Blueprint $table) {
            $table->dropForeignKeyConstraints();
            $table->dropIndex('google_drive_files_google_drive_sub_category_id_index');
            $table->dropIndex('google_drive_files_year_index');
            $table->dropColumn(['google_drive_sub_category_id', 'year']);
        });
    }
};
