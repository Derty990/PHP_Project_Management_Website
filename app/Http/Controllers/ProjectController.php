<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    // w app/Http/Controllers/ProjectController.php
    public function index(Request $request) // <-- Dodaj Request $request
    {
        // Pobieram zapytanie do bazy, ale jeszcze go nie wykonuję
        $query = Project::where('user_id', auth()->id());

        // Jeśli w adresie URL jest parametr 'search'
        if ($request->has('search')) {
            // Dodaję do zapytania warunek wyszukiwania
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Na końcu wykonuję zapytanie i pobieram wyniki
        $projects = $query->get();

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
        // 1. WALIDACJA DANYCH
        // Jeśli walidacja się nie powiedzie, Laravel automatycznie cofnie
        // użytkownika do formularza i wyświetli komunikaty o błędach.
        $validatedData = $request->validate([
            // Pole 'name' jest wymagane, musi być tekstem i mieć maksymalnie 255 znaków.
            'name' => 'required|string|max:255',
            // Pole 'description' nie jest wymagane, ale jeśli jest podane, to musi być tekstem.
            'description' => 'nullable|string',
        ]);

        // 2. PRZYGOTOWANIE DANYCH DO ZAPISU
        // Do zwalidowanych danych z formularza dodaje ID aktualnie zalogowanego użytkownika.
        // To jest kluczowe, aby każdy projekt miał swojego właściciela.
        $dataToStore = $validatedData;
        $dataToStore['user_id'] = Auth::id();

        // 3. STWORZENIE NOWEGO PROJEKTU W BAZIE DANYCH
        // metoda create(), która masowo przypisze dane z przygotowanej tablicy.
        // Działa to bezpiecznie dzięki temu, że zdefiniowałem właściwość `$fillable` w modelu Project.
        Project::create($dataToStore);

        // 4. PRZEKIEROWANIE Z WIADOMOŚCIĄ O SUKCESIE
        // Przekierowuje użytkownika na stronę z listą projektów
        // z tzw. "flash message", czyli jednorazową wiadomością.
        return redirect()->route('projects.index')
            ->with('success', 'Projekt został pomyślnie utworzony!');
    }

    public function show(Project $project)
    {
        // Sprawdź autoryzację, czy użytkownik może widzieć ten projekt
        if (auth()->id() !== $project->user_id) {
            abort(403);
        }
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        // Laravel automatycznie znajdzie projekt o danym ID dzięki "Route Model Binding"
        // Przekazuje znaleziony projekt do widoku 'edit'
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
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
        // 1. AUTORYZACJA (bardzo ważny krok!)
        // Upewniam się, że użytkownik, który próbuje usunąć projekt,
        // jest jego faktycznym właścicielem.
        if (auth()->id() !== $project->user_id) {
            // Jeśli nie jest właścicielem, przerywam i zwracam błąd.
            // 403 to błąd "Forbidden" (Brak dostępu).
            abort(403);
        }

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
        // Waliduję, czy podany email istnieje w tabeli users
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Znajduję użytkownika o podanym adresie email
        $user = User::where('email', $validated['email'])->first();

        // Sprawdzam, czy ten użytkownik nie jest już członkiem projektu
        if ($project->members->contains($user)) {
            return back()->withErrors(['email' => 'Ten użytkownik jest już członkiem tego projektu.']);
        }

        // Dodaję powiązanie w tabeli pośredniczącej (pivot)
        // Metoda attach() jest przeznaczona specjalnie dla relacji wiele-do-wielu
        $project->members()->attach($user->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Dodano nowego członka do projektu!');
    }

    public function removeMember(Project $project, User $user)
    {
        // Autoryzacja: tylko właściciel projektu może usuwać członków
        if (auth()->id() !== $project->user_id) {
            abort(403);
        }

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
