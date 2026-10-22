<?php

namespace App\Enums;

enum MovieStatus: string
{
    case NOW_SHOWING = 'now_showing';
    case COMING_SOON = 'coming_soon';
    case ARCHIVED = 'archived';
}

