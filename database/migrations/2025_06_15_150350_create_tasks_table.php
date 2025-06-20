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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('todo'); // np. 'todo', 'in_progress', 'done'
            $table->string('priority')->default('medium'); // np. 'low', 'medium', 'high'
            $table->date('due_date')->nullable(); // Termin wykonania

            // --- RELACJA ---
            // Każde zadanie musi należeć do jakiegoś projektu.
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
