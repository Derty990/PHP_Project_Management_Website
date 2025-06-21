<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    /**
     * Dodanie Traita AuthorizesRequests
     * W PHP i Laravelu robi się to za pomocą tak zwanych Traitów. To takie "paczki z gotowymi funkcjami", które można "wstrzyknąć"
     * do dowolnej klasy. Trait, którego potrzeba, nazywa się AuthorizesRequests i dostarcza on m.in. metodę authorize().
     */
    use AuthorizesRequests;

    public function create(Project $project)
    {
        // Sprawdzam, czy użytkownik może tworzyć zadania w tym projekcie
        $this->authorize('create', [Task::class, $project]);
        // Zwracam widok formularza, przekazując do niego projekt,
        // żeby wiedziec, do jakiego projektu dodaje zadanie.
        return view('tasks.create', compact('project'));
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Chcę zadania, KTÓRE MAJĄ powiązany projekt ('project'), który spełnia warunki...
        $query = Task::whereHas('project', function ($projectQuery) use ($user) {
            // ...warunki są takie: właścicielem projektu jest zalogowany użytkownik
            $projectQuery->where('user_id', $user->id)
                // LUB zalogowany użytkownik jest członkiem tego projektu
                ->orWhereHas('members', function ($memberQuery) use ($user) {
                    $memberQuery->where('user_id', $user->id);
                });
        });

        // Jeśli w adresie URL jest parametr 'search'
        if ($request->filled('search')) {
            // Filtruję po TYTULE zadania ('title'), a nie po 'name'
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        // Pobieram posortowane wyniki
        $tasks = $query->with('project')->latest()->get();

        // Przekazuję zadania do widoku
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request, Project $project)
    {

        // Sprawdzam uprawnienia, zanim zapiszę zadanie
        $this->authorize('create', [Task::class, $project]);

        // Nowe, rozbudowane reguły walidacji
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(TaskStatus::class)],
            'priority' => ['required', new Enum(TaskPriority::class)],
        ]);

        $project->tasks()->create($validatedData);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Zadanie zostało pomyślnie dodane.');
    }

    public function edit(Task $task)
    {
        // Sprawdzam, czy użytkownik może aktualizować to konkretne zadanie
        $this->authorize('update', $task);

        // Przekazuję do widoku zadanie, które edytuję,
        // oraz wszystkie możliwe opcje statusów i priorytetów z moich Enumów.
        return view('tasks.edit', [
            'task' => $task,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }

    // NOWY KOD
    public function update(Request $request, Task $task)
    {
        // Sprawdzam uprawnienia przed aktualizacją
        $this->authorize('update', $task);

        // Waliduję dane, w tym nowe pola status i priority
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'status' => ['required', new Enum(TaskStatus::class)],
            'priority' => ['required', new Enum(TaskPriority::class)],
        ]);

        // Aktualizuję zadanie w bazie danych
        $task->update($validatedData);

        // Przekierowuję użytkownika z powrotem na stronę szczegółów projektu
        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Zadanie zostało pomyślnie zaktualizowane.');
    }

    public function destroy(Task $task)
    {
        // Sprawdzam uprawnienia przed usunięciem
        $this->authorize('delete', $task);

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

    public function show(Task $task)
    {
        // Sprawdzam, czy użytkownik ma uprawnienie 'view' zdefiniowane w TaskPolicy
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'zadania-' . now()->format('Y-m-d') . '.csv';

        // Używam tej samej logiki co w metodzie index do pobrania zadań użytkownika
        $tasks = Task::whereHas('project', function ($projectQuery) {
            $projectQuery->where('user_id', auth()->id())
                ->orWhereHas('members', function ($memberQuery) {
                    $memberQuery->where('user_id', auth()->id());
                });
        })->with('project')->get();

        // Nagłówki dla pliku CSV
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Kolumny w CSV
        $columns = ['Tytuł', 'Projekt', 'Status', 'Priorytet', 'Termin'];

        // Funkcja zwrotna (callback), która generuje CSV w locie
        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['Tytuł']     = $task->title;
                $row['Projekt']   = $task->project->name;
                $row['Status']    = $task->status->label();
                $row['Priorytet'] = $task->priority->label();
                $row['Termin']    = $task->due_date ? $task->due_date->format('Y-m-d') : 'Brak';

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        // Zwracam odpowiedź, która rozpocznie pobieranie pliku
        return response()->stream($callback, 200, $headers);
    }


}
