<?php

namespace App\Models;

use App\Enums\MonitorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Monitor extends Model
{
    Use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'is_active',
        'interval',
        'timeout',
    ];

    protected $casts = [
        'type' => MonitorType::class,
        'is_active' => 'boolean',
        'interval' => 'integer',
        'timeout' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function httpMonitorConfig(): HasOne
    {
        return $this->hasOne(HttpMonitorConfig::class);
    }

    public function tcpMonitorConfig(): HasOne
    {
        return $this->hasOne(TcpMonitorConfig::class);
    }

    public function monitorState(): HasOne
    {
        return $this->hasOne(MonitorState::class);
    }

    public function getConfigAttribute()
    {
        return match ($this->type) {
            MonitorType::HTTP => $this->httpMonitorConfig,
            MonitorType::TCP => $this->tcpMonitorConfig,
            default => null,
        };
    }   

    public static function createWithConfig(array $data, array $configData): self
    {
        return DB::transaction(function () use ($data, $configData) {
            $monitor = self::create($data);

            MonitorState::crateInitialState($monitor);

            if ($monitor->type === MonitorType::HTTP) {
                $monitor->httpMonitorConfig()->create($configData);
            } elseif ($monitor->type === MonitorType::TCP) {
                $monitor->tcpMonitorConfig()->create($configData);
            } else {
                throw new InvalidArgumentException('Invalid monitor type');
            }

            return $monitor;
        });
    }

    public static function updateWithConfig(Monitor $monitor, array $data, array $configData): self
    {
        return DB::transaction(function () use ($monitor, $data, $configData) {

            if($monitor->type->value != $data['type'] ?? $monitor->type) {
                throw new InvalidArgumentException('Cannot change monitor type');
            }

            $monitor->update($data);

            if ($monitor->type === MonitorType::HTTP) {
                if ($monitor->httpMonitorConfig) {
                    $monitor->httpMonitorConfig()->update($configData);
                } else {
                    $monitor->httpMonitorConfig()->create($configData);
                }
            } elseif ($monitor->type === MonitorType::TCP) {
                if ($monitor->tcpMonitorConfig) {
                    $monitor->tcpMonitorConfig()->update($configData);
                } else {
                    $monitor->tcpMonitorConfig()->create($configData);
                }
            } else {
                throw new InvalidArgumentException('Invalid monitor type');
            }

            return $monitor->fresh();
        });
    }
}
