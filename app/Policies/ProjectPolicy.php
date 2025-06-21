<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Sprawdza, czy użytkownik może wyświetlić szczegóły projektu.
     */
    public function view(User $user, Project $project): bool
    {
        // Użytkownik może zobaczyć projekt, jeśli jest jego właścicielem LUB jest jego członkiem.
        return $user->id === $project->user_id || $project->members->contains($user);
    }

    /**
     * Sprawdza, czy użytkownik może zaktualizować projekt (np. zmienić nazwę, zarządzać członkami).
     */
    public function update(User $user, Project $project): bool
    {
        // Tylko właściciel projektu może go w pełni modyfikować.
        return $user->id === $project->user_id;
    }

    /**
     * Sprawdza, czy użytkownik może usunąć projekt.
     */
    public function delete(User $user, Project $project): bool
    {
        // Tylko właściciel projektu może go usunąć.
        return $user->id === $project->user_id;
    }

    // Można tu w przyszłości dodać więcej metod, np. dla edytorów:
    // public function manageTasks(User $user, Project $project): bool
    // {
    //     $role = $project->members()->where('user_id', $user->id)->first()?->pivot->role;
    //     return $user->id === $project->user_id || $role === 'editor';
    // }
}
