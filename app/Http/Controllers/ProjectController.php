<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{

    /**
     * Dodanie Traita AuthorizesRequests
     * W PHP i Laravelu robi się to za pomocą tak zwanych Traitów. To takie "paczki z gotowymi funkcjami", które można "wstrzyknąć"
     * do dowolnej klasy. Trait, którego potrzeba, nazywa się AuthorizesRequests i dostarcza on m.in. metodę authorize().
     */
    use AuthorizesRequests;
    /**
     * Wyświetla listę projektów.
     * Na razie pokazuje tylko projekty, których zalogowany użytkownik jest właścicielem.
     * Później możemy to rozbudować, aby pokazywało też projekty, w których jest członkiem.
     */
    public function index(Request $request)
    {
        // Pobieram obiekt zalogowanego użytkownika raz, dla czystości kodu
        $user = $request->user();

        // Zaczynam budować zapytanie. Chcę projekty, które:
        // 1. Użytkownik jest właścicielem (user_id zgadza się z ID zalogowanego usera)
        // LUB
        // 2. Użytkownik jest członkiem (istnieje powiązanie w tabeli pivot `project_user`)
        $query = Project::where('user_id', $user->id)
            ->orWhereHas('members', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            });

        // Jeśli w adresie URL jest parametr 'search'
        if ($request->filled('search')) {
            // Dodatkowo filtruję po nazwie projektu
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Pobieram posortowane wyniki (najnowsze na górze)
        $projects = $query->latest()->get();

        // Przekazuję projekty do widoku
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ta metoda ma tylko jedno zadanie: pokazać formularz tworzenia projektu
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Walidacja danych z formularza
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
        ]);

        // 2. Stworzenie projektu przez relację
        // Ta linia automatycznie pobiera zalogowanego użytkownika,
        // tworzy nowy projekt i od razu przypisuje do niego poprawne `user_id`.
        auth()->user()->projects()->create($validatedData);

        // 3. Przekierowanie z komunikatem o sukcesie
        return redirect()->route('projects.index')
            ->with('success', 'Projekt został pomyślnie utworzony!');
    }

    /**
     * Wyświetla szczegóły konkretnego projektu.
     */
    public function show(Project $project, Request $request) // <-- Dodaję Request
    {
        // Autoryzacja - sprawdzam, czy użytkownik może widzieć ten projekt
        $this->authorize('view', $project);

        // --- Logika pobierania zadań z filtrowaniem ---

        // Zaczynam budować zapytanie dla zadań TYLKO z tego konkretnego projektu
        $tasksQuery = $project->tasks()->latest();

        // Jeśli w adresie URL jest parametr 'search_tasks'
        if ($request->filled('search_tasks')) {
            // Dodaję do zapytania warunek wyszukiwania po tytule zadania
            $tasksQuery->where('title', 'like', '%' . $request->input('search_tasks') . '%');
        }

        // Na końcu wykonuję zapytanie i pobieram zadania
        $tasks = $tasksQuery->get();

        // --- Koniec logiki zadań ---

        // Pobieram role do formularza dodawania członków (to co było wcześniej)
        $roles = \App\Models\Role::where('name', '!=', 'owner')->get();

        // Przekazuję do widoku projekt, role ORAZ naszą nową, przefiltrowaną listę zadań
        return view('projects.show', compact('project', 'roles', 'tasks'));
    }

    /**
     * Pokazuje formularz do edycji projektu.
     */
    public function edit(Project $project)
    {
        // Sprawdzam uprawnienie 'update' z ProjectPolicy (bo edycja prowadzi do aktualizacji)
        $this->authorize('update', $project);
        // Laravel automatycznie znajdzie projekt o danym ID dzięki "Route Model Binding"
        // Przekazuje znaleziony projekt do widoku 'edit'
        return view('projects.edit', compact('project'));
    }

    /**
     * Aktualizuje projekt w bazie danych.
     */
    public function update(Request $request, Project $project)
    {
        // Sprawdzam uprawnienie 'update'
        $this->authorize('update', $project);

        // 1. Walidacja danych
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 2. Aktualizacja projektu w bazie danych
        $project->update($validatedData);

        // 3. Przekierowanie z powrotem na listę projektów z komunikatem
        return redirect()->route('projects.index')
            ->with('success', 'Projekt został pomyślnie zaktualizowany.');
    }

    public function destroy(Project $project)
    {
        // Sprawdzam uprawnienie 'delete'
        $this->authorize('delete', $project);

       /* // 1. AUTORYZACJA (bardzo ważny krok!)
        // Upewniam się, że użytkownik, który próbuje usunąć projekt,
        // jest jego faktycznym właścicielem.
        if (auth()->id() !== $project->user_id) {
            // Jeśli nie jest właścicielem, przerywam i zwracam błąd.
            // 403 to błąd "Forbidden" (Brak dostępu).
            abort(403);
        }*/

        // 2. USUNIĘCIE (MIĘKKIE)
        // Dzięki temu, że model Project używa traita "SoftDeletes",
        // ta komenda nie usunie rekordu z bazy, a jedynie ustawi
        // datę w kolumnie `deleted_at`.
        $project->delete();

        // 3. PRZEKIEROWANIE
        // Wracam na listę projektów z komunikatem o sukcesie.
        return redirect()->route('projects.index')
            ->with('success', 'Projekt został pomyślnie usunięty.');
    }

    public function addMember(Request $request, Project $project)
    {
        // 1. Sprawdzam, czy właściciel ma prawo zarządzać projektem
        $this->authorize('update', $project);

        // 2. Waliduję dane wejściowe
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        // 3. Znajduję użytkownika, którego chcę dodać
        $user = User::where('email', $validated['email'])->first();

        // 4. Sprawdzam, czy użytkownik nie jest już członkiem projektu, aby uniknąć duplikatów
        if ($project->members->contains($user)) {
            // Jeśli tak, cofam z błędem
            return back()->withErrors(['email' => 'Ten użytkownik jest już członkiem tego projektu.']);
        }

        // 5. Dodaję powiązanie w tabeli pośredniczącej 'project_user'
        // Metoda attach() zapisuje user_id i dodatkowe dane (role_id) w tabeli pivot.
        $project->members()->attach($user->id, ['role_id' => $validated['role_id']]);

        // 6. Przekierowuję z powrotem z komunikatem o sukcesie
        return redirect()->route('projects.show', $project)
            ->with('success', 'Dodano nowego członka do projektu!');
    }


    public function removeMember(Project $project, User $user)
    {

        // Sprawdzam uprawnienie 'update'
        $this->authorize('update', $project);

        /*// Autoryzacja: tylko właściciel projektu może usuwać członków
        if (auth()->id() !== $project->user_id) {
            abort(403);
        }*/

        // Nie pozwól właścicielowi usunąć samego siebie z listy członków
        if ($project->user_id === $user->id) {
            return back()->withErrors(['email' => 'Nie możesz usunąć właściciela projektu.']);
        }

        // Usuwam powiązanie w tabeli pośredniczącej (pivot)
        // Metoda detach() jest przeznaczona specjalnie dla relacji wiele-do-wielu
        $project->members()->detach($user->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Usunięto członka z projektu.');
    }


}
