<?php

namespace App\Enums;

enum MonitorType: string
{
    case HTTP = 'http';
    case TCP = 'tcp';

}
