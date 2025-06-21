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
            // 1. Usuwam starą kolumnę 'role'
            $table->dropColumn('role');
            // 2. Dodaje nową kolumnę 'role_id', która będzie kluczem obcym
            $table->foreignId('role_id')->after('user_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            // Logika odwrotna: usuwam klucz obcy i kolumnę, dodaje starą
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->string('role')->default('member');
        });
    }
};
