<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        // Jeden projekt "ma wiele" zadań
        return $this->hasMany(Task::class);

    }

    public function members()
    {
        // Projekt "należy do wielu" użytkowników
        return $this->belongsToMany(User::class);
    }

}
