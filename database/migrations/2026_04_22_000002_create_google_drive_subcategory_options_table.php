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
        Schema::create('google_drive_subcategory_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('google_drive_sub_category_id');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            // Indexes
            $table->foreign('google_drive_sub_category_id')
                ->references('id')
                ->on('google_drive_sub_categories')
                ->onDelete('cascade');
            $table->index('google_drive_sub_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_drive_subcategory_options');
    }
};
