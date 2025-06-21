<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'due_date' => 'date',
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
    ];

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

    public function attachments()
    {
        // Jedno zadanie może mieć wiele załączników
        return $this->hasMany(Attachment::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest(); // `latest()` posortuje od najnowszych
    }

}
