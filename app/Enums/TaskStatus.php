<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    // Opcjonalnie: Można dodać metodę, aby uzyskać "ładną" etykietę
    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Do zrobienia',
            self::InProgress => 'W trakcie',
            self::Done => 'Zakończone',
        };
    }
}
