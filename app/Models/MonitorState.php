<?php

namespace App\Models;

use App\Enums\StatusMonitor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class MonitorState extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'monitor_id',
        'status',
        'consecutive_failures',
        'last_checked_at',
        'last_successful_check_at',
        'last_failed_check_at',
    ];

    protected $casts = [
        'status' => StatusMonitor::class,
        'consecutive_failures' => 'integer',
        'last_checked_at' => 'datetime',
        'last_successful_check_at' => 'datetime',
        'last_failed_check_at' => 'datetime',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public static function createInitialState(Monitor $monitor): self
    {
        return self::create([
            'monitor_id' => $monitor->id,
            'status' => StatusMonitor::UNKNOWN,
            'consecutive_failures' => 0,
            'last_checked_at' => null,
            'last_successful_check_at' => null,
            'last_failed_check_at' => null,
        ]);
    }
}
