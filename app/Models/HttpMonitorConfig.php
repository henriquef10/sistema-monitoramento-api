<?php

namespace App\Models;

use App\Enums\HttpMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class HttpMonitorConfig extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'monitor_id',
        'url',
        'method',
        'follow_redirects',
        'expected_status_codes',
        'expected_response_body',
        'max_response_time_ms',
        'keyword_contains',
        'keyword_not_contains',
    ];

    protected $casts = [
        'method' => HttpMethod::class,
        'follow_redirects' => 'boolean',
        'expected_status_codes' => 'array',
        'expected_response_body' => 'array',
        'max_response_time_ms' => 'integer',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

}
