<?php

namespace App\Enum;

enum TodoPriority : string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
