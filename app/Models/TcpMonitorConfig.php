<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class TcpMonitorConfig extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'monitor_id',
        'host',
        'port',
        'max_connect_time_ms',
    ];

    protected $casts = [
        'port' => 'integer',
        'max_connect_time_ms' => 'integer',
    ];
    
    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

}
