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
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // Klucz główny 'id'

            // --- POCZĄTEK  NOWYCH KOLUMN ---

            // Kolumna na nazwę projektu (VARCHAR)
            $table->string('name');

            // Kolumna na dłuższy opis projektu (TEXT). Może być pusta (nullable).
            $table->text('description')->nullable();

            // Kolumna na status. Domyślnie każdy nowy projekt będzie 'aktywny'.
            $table->string('status')->default('active'); // np. 'active', 'completed', 'on_hold'

            // Klucz obcy łączący projekt z użytkownikiem (właścicielem)
            // constrained() - automatycznie łączy z tabelą 'users' i kolumną 'id'
            // onDelete('cascade') - jeśli usunę użytkownika, jego projekty też znikną
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // --- KONIEC NOWYCH KOLUMN ---

            // Tworzy kolumny 'created_at' i 'updated_at'
            $table->timestamps();

            // Dodaje kolumnę 'deleted_at' do obsługi "miękkiego usuwania"
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
