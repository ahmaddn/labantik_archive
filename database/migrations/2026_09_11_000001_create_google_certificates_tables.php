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
        Schema::create('google_certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('certificate_number_format')->nullable();
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->string('background_image')->nullable();
            
            // Toggle Switches
            $table->boolean('show_header')->default(true);
            $table->boolean('show_number')->default(true);
            $table->boolean('show_back_page')->default(true);
            
            // Header logos & text
            $table->string('header_left_logo')->nullable();
            $table->string('header_right_logo')->nullable();
            $table->text('header_title')->nullable();
            $table->text('header_subtitle')->nullable();
            
            // Main Front Page Content
            $table->string('main_title')->default('SERTIFIKAT');
            $table->string('sub_title')->default('Diberikan kepada');
            $table->string('role_caption')->nullable()->comment('Contoh: Sebagai Peserta / Sebagai {role_name}');
            $table->longText('content_text')->nullable();
            $table->string('place_date')->nullable();
            
            // Signer 1 (Front Page Signer, e.g. Kepala Sekolah)
            $table->string('signer_1_title')->default('Kepala Sekolah');
            $table->uuid('signer_1_employee_id')->nullable();
            
            // Back Page Content (Struktur Program)
            $table->string('back_page_title')->nullable();
            
            // Signer 2 (Back Page Signer, e.g. Ketua Pelaksana)
            $table->string('signer_2_title')->default('Ketua Pelaksana');
            $table->uuid('signer_2_employee_id')->nullable();
            
            $table->enum('status', ['active', 'draft'])->default('active');
            
            // New options: recipient selection & wordart
            $table->enum('recipient_type', ['peserta', 'narasumber'])->default('peserta');
            $table->string('custom_recipient_name')->nullable();
            $table->string('word_art_style')->default('none');
            
            $table->timestamps();

            if (Schema::hasTable('core_employees')) {
                $table->foreign('signer_1_employee_id')
                    ->references('id')
                    ->on('core_employees')
                    ->nullOnDelete();

                $table->foreign('signer_2_employee_id')
                    ->references('id')
                    ->on('core_employees')
                    ->nullOnDelete();
            }
        });

        Schema::create('google_certificate_structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('certificate_id');
            $table->integer('sort_order')->default(0);
            $table->string('materi_name');
            $table->string('time_allocation')->nullable();
            $table->timestamps();

            $table->foreign('certificate_id')
                ->references('id')
                ->on('google_certificates')
                ->onDelete('cascade');
        });

        Schema::create('google_certificate_roles', function (Blueprint $table) {
            $table->uuid('certificate_id');
            $table->string('role_id');

            $table->primary(['certificate_id', 'role_id']);

            $table->foreign('certificate_id')
                ->references('id')
                ->on('google_certificates')
                ->onDelete('cascade');

            if (Schema::hasTable('core_roles')) {
                $table->foreign('role_id')
                    ->references('id')
                    ->on('core_roles')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_certificate_roles');
        Schema::dropIfExists('google_certificate_structures');
        Schema::dropIfExists('google_certificates');
    }
};
