<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'body',
        'user_id',
        'task_id',
    ];

    /**
     * Definiuje relację "należy do" z modelem User.
     * Każdy komentarz został napisany przez jednego użytkownika.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Definiuje relację "należy do" z modelem Task.
     * Każdy komentarz jest przypisany do jednego zadania.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
