<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class QueueSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'prefix', 'avg_service_minutes', 'reset_daily',
        'current_counter', 'last_reset_date',
        'max_queue_limit', 'is_system_open', 'youtube_url',
    ];

    protected function casts(): array
    {
        return [
            'reset_daily' => 'boolean',
            'is_system_open' => 'boolean',
            'last_reset_date' => 'date',
        ];
    }

    // Method kunci
    public function generateNextNumber(): string
    {
        // 1. Jika reset_daily=true dan last_reset_date bukan hari ini → reset counter ke 0
        if ($this->reset_daily && (! $this->last_reset_date || ! $this->last_reset_date->isToday())) {
            $this->current_counter = 0;
            $this->last_reset_date = Carbon::today();
        }

        // 2. Increment current_counter
        $this->current_counter++;

        // 3. Save
        $this->save();

        // 4. Return prefix + str_pad(current_counter, 3, '0', STR_PAD_LEFT)
        return $this->prefix.str_pad($this->current_counter, 3, '0', STR_PAD_LEFT);
    }
}
