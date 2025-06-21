<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Wyłączam ochronę, bo ta tabela będzie zarządzana tylko przez nas (programistów)
    protected $guarded = [];
}
