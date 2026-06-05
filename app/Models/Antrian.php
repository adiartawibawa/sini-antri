<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Antrian extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'queues';

    protected $fillable = [
        'uuid', 'queue_number', 'queue_order',
        'visitor_name', 'purpose', 'status',
        'operator_id', 'called_at', 'served_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // Hitung antrian waiting dengan queue_order < queue_order milik record ini
    public function getPositionAheadAttribute(): int
    {
        return static::where('status', 'waiting')
            ->where('queue_order', '<', $this->queue_order)
            ->count();
    }

    // position_ahead × avg_service_minutes dari QueueSetting
    public function getEstimatedWaitMinutesAttribute(): int
    {
        $avgMinutes = QueueSetting::first()?->avg_service_minutes ?? 5;

        return $this->position_ahead * $avgMinutes;
    }

    // WHERE status = 'waiting' ORDER BY queue_order ASC
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting')->orderBy('queue_order', 'asc');
    }

    // WHERE status IN ('waiting', 'called')
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'called']);
    }
}
