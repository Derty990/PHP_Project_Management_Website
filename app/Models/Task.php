<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'project_id',
    ];

    public function project()
    {
        // Jedno zadanie "należy do" jednego projektu
        return $this->belongsTo(Project::class);
    }

    public function comments() { return $this->hasMany(Comment::class); }
}
