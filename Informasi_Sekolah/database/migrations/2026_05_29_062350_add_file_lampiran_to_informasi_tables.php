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
        Schema::table('hari_liburs', function (Blueprint $table) {
            $table->string('file_lampiran')->nullable();
        });
        Schema::table('kegiatan_sekolahs', function (Blueprint $table) {
            $table->string('file_lampiran')->nullable();
        });
        Schema::table('jadwal_rapots', function (Blueprint $table) {
            $table->string('file_lampiran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hari_liburs', function (Blueprint $table) {
            $table->dropColumn('file_lampiran');
        });
        Schema::table('kegiatan_sekolahs', function (Blueprint $table) {
            $table->dropColumn('file_lampiran');
        });
        Schema::table('jadwal_rapots', function (Blueprint $table) {
            $table->dropColumn('file_lampiran');
        });
    }
};
