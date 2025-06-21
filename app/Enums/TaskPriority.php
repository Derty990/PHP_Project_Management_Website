<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Niski',
            self::Medium => 'Średni',
            self::High => 'Wysoki',
        };
    }
}
