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
        Schema::table('project_user', function (Blueprint $table) {
            // Dodaję nową kolumnę 'role' typu string.
            // Domyślnie każdy nowy członek będzie miał rolę 'member'.
            // after('user_id') umieści tę kolumnę w bazie zaraz po user_id dla porządku.
            $table->string('role')->default('member')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
