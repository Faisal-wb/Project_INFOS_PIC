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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telp', 20)->nullable()->after('email');
            $table->text('about_me')->nullable()->after('no_telp');
            $table->string('foto_profile')->nullable()->after('about_me');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_telp', 'about_me', 'foto_profile']);
        });
    }
};
