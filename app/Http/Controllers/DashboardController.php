<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Wyświetla główny panel użytkownika.
     */
    public function index(): View
    {
        $user = auth()->user();
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        $projectIds = $projects->pluck('id');

        // Pobieram wszystkie zadania do przetworzenia
        $allTasks = Task::whereIn('project_id', $projectIds)->get();

        // Grupuję zadania po statusie i zliczam je
        $tasksByStatus = $allTasks->groupBy('status.value')->map->count();

        // Przygotowuję dane dla wykresu
        $chartLabels = $tasksByStatus->keys()->map(fn ($status) => TaskStatus::from($status)->label())->toArray();
        $chartData = $tasksByStatus->values()->toArray();

        return view('dashboard', [
            'projectCount' => $projects->count(),
            'taskCount' => $allTasks->count(),
            'recentTasks' => $allTasks->sortByDesc('created_at')->take(5),
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }
}
