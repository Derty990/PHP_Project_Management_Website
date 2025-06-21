<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Sprawdza, czy użytkownik może tworzyć nowe zadania w danym projekcie.
     */
    public function create(User $user, Project $project): bool
    {
        // Pozwalam, jeśli użytkownik jest właścicielem LUB ma rolę 'editor'
        return $user->id === $project->user_id || $this->hasEditorRole($user, $project);
    }

    /**
     * Sprawdza, czy użytkownik może aktualizować zadanie.
     */
    public function update(User $user, Task $task): bool
    {

        // Logika jest taka sama: sprawdzam uprawnienia w kontekście projektu, do którego należy zadanie
        return $user->id === $task->project->user_id || $this->hasEditorRole($user, $task->project);
    }

    /**
     * Sprawdza, czy użytkownik może usunąć zadanie.
     */
    public function delete(User $user, Task $task): bool
    {
        // Taka sama logika jak przy aktualizacji
        return $user->id === $task->project->user_id || $this->hasEditorRole($user, $task->project);
    }

    /**
     * Pomocnicza, prywatna metoda do sprawdzania roli 'editor'.
     */
    private function hasEditorRole(User $user, Project $project): bool
    {
        $member = $project->members()->where('user_id', $user->id)->first();

        // Jeśli nie jest członkiem, na pewno nie ma uprawnień
        if (!$member) {
            return false;
        }

        // Pobieram ID roli 'editor' z bazy
        $editorRole = Role::where('name', 'editor')->first();

        // Zwracam true, jeśli rola użytkownika to rola edytora
        return $member->pivot->role_id === $editorRole->id;
    }

    /**
     * Sprawdza, czy użytkownik może wyświetlić szczegóły zadania.
     */
    public function view(User $user, Task $task): bool
    {
        // Użytkownik może zobaczyć zadanie, jeśli jest właścicielem projektu,
        // LUB jest członkiem projektu, do którego należy to zadanie.
        return $user->id === $task->project->user_id || $task->project->members->contains($user);
    }

    /**
     * Sprawdza, czy użytkownik może dodać komentarz do zadania.
     */
    public function addComment(User $user, Task $task): bool
    {
        // Logika jest taka sama jak przy wyświetlaniu: pozwalam, jeśli użytkownik
        // jest właścicielem projektu LUB jest jego członkiem.
        return $user->id === $task->project->user_id || $task->project->members->contains($user);
    }
}
