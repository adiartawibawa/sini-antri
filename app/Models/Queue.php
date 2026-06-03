<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid', 'queue_number', 'queue_order',
        'visitor_name', 'purpose', 'status',
        'operator_id', 'called_at', 'served_at', 'completed_at',
    ];

    protected function casts()
    {
        return [
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Hitung berapa antrian di depan pengunjung ini
    public function getPositionAheadAttribute(): int
    {
        return static::where('status', 'waiting')
            ->where('queue_order', '<', $this->queue_order)
            ->count();
    }

    // Estimasi waktu tunggu dalam menit
    public function getEstimatedWaitMinutesAttribute(): int
    {
        $avgMinutes = QueueSetting::first()?->avg_service_minutes ?? 5;

        return $this->position_ahead * $avgMinutes;
    }

    // Scope: antrian yang sedang aktif
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'called']);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting')->orderBy('queue_order');
    }
}
