<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        // Zwracam widok formularza, przekazując do niego projekt,
        // żeby wiedziec, do jakiego projektu dodaje zadanie.
        return view('tasks.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Tworze nowe zadanie, korzystając z relacji, którą zdefiniowalem
        // Laravel automatycznie ustawi `project_id` na ID projektu, do którego
        // dodaje zadanie. To jest czyste i eleganckie.
        $project->tasks()->create($validatedData);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Zadanie zostało pomyślnie dodane.');
    }

    public function edit(Task $task)
    {
        // Ta metoda po prostu pokazuje formularz edycji,
        // przekazując do niego zadanie, które chcę edytować.
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        // Waliduję dane przychodzące z formularza edycji.
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Aktualizuję zadanie w bazie danych na podstawie zwalidowanych danych.
        $task->update($validatedData);

        // Przekierowuję użytkownika z powrotem na stronę szczegółów projektu,
        // do którego należy to zadanie, z komunikatem o sukcesie.
        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Zadanie zostało pomyślnie zaktualizowane.');
    }

    public function destroy(Task $task)
    {
        // Sprawdzam, czy zalogowany użytkownik jest właścicielem projektu,
        // do którego należy to zadanie. To prosta autoryzacja.
        if (auth()->id() !== $task->project->user_id) {
            abort(403);
        }

        // Wykonuję "miękkie" usunięcie. Laravel ustawi datę w kolumnie `deleted_at`.
        $task->delete();

        // Przekierowuję z powrotem na stronę projektu z komunikatem o sukcesie.
        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Zadanie zostało pomyślnie usunięte.');
    }
}
