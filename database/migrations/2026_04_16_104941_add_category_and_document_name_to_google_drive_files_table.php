<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('google_drive_files', function (Blueprint $table) {
            $table->string('document_name')->after('user_id'); // Nama custom dari user
            $table->uuid('google_category_id')->nullable()->after('document_name');
            $table->uuid('expertise_id')->nullable()->after('google_category_id');

            // Foreign keys
            $table->foreign('google_category_id')->references('id')->on('google_drive_categories')->onDelete('set null');
            $table->foreign('expertise_id')->references('id')->on('core_expertise_concentrations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('google_drive_files', function (Blueprint $table) {
            $table->dropForeign(['google_category_id']);
            $table->dropForeign(['expertise_id']);
            $table->dropColumn(['document_name', 'google_category_id', 'expertise_id']);
        });
    }
};
