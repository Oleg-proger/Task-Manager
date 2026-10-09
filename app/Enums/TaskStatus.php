<?php

namespace App\Enums;

enum TaskStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Done = 'done';
}
