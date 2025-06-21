<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }


    protected array $policies = [
        Project::class => ProjectPolicy::class,
        Task::class => TaskPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    // NOWY KOD
    public function boot(): void
    {
        /*// Definiuję "bramkę" o nazwie 'manage-project'.
        // Przyjmuje ona zalogowanego użytkownika ($user) i projekt ($project), którego dotyczy.
        Gate::define('manage-project', function (User $user, Project $project) {
            // Zwraca 'true', jeśli ID użytkownika jest takie samo jak ID właściciela projektu.
            // W przeciwnym wypadku zwraca 'false'.
            return $user->id === $project->user_id;
        });*/
    }
}
