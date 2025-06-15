<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pobierz projekty NALEŻĄCE tylko do aktualnie zalogowanego użytkownika
        $projects = Project::where('user_id', auth()->id())->get();

        // Przekaż pobrane projekty do widoku i go wyświetl
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


}
