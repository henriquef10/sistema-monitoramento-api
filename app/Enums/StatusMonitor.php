<?php

namespace App\Enums;

enum StatusMonitor: string
{
    case UP = 'UP';
    case DOWN = 'DOWN';
    case UNKNOWN = 'UNKNOWN';
}
