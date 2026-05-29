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
            $table->string('gambar')->nullable();
        });
        
        Schema::table('kegiatan_sekolahs', function (Blueprint $table) {
            $table->string('gambar')->nullable();
        });
        
        Schema::table('jadwal_rapots', function (Blueprint $table) {
            $table->string('gambar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hari_liburs', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
        
        Schema::table('kegiatan_sekolahs', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
        
        Schema::table('jadwal_rapots', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
