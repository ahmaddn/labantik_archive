<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('google_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('google_certificates', 'recipient_type')) {
                $table->enum('recipient_type', ['peserta', 'narasumber'])->default('peserta')->after('status');
            }
            if (!Schema::hasColumn('google_certificates', 'custom_recipient_name')) {
                $table->string('custom_recipient_name')->nullable()->after('recipient_type');
            }
            if (!Schema::hasColumn('google_certificates', 'word_art_style')) {
                $table->string('word_art_style')->default('none')->after('custom_recipient_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('google_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('google_certificates', 'recipient_type')) {
                $table->dropColumn('recipient_type');
            }
            if (Schema::hasColumn('google_certificates', 'custom_recipient_name')) {
                $table->dropColumn('custom_recipient_name');
            }
            if (Schema::hasColumn('google_certificates', 'word_art_style')) {
                $table->dropColumn('word_art_style');
            }
        });
    }
};
