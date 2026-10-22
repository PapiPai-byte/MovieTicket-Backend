<?php

namespace App\Enums;

enum ShowtimeStatus: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}

