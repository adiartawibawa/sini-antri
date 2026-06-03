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
    ];

    protected function casts()
    {
        return [
            'reset_daily' => 'boolean',
            'last_reset_date' => 'date',
        ];
    }

    // Generate nomor antrian berikutnya
    public function generateNextNumber(): string
    {
        // Reset jika hari berbeda
        if ($this->reset_daily && $this->last_reset_date?->isToday() === false) {
            $this->current_counter = 0;
            $this->last_reset_date = Carbon::today();
        }

        $this->current_counter++;
        $this->save();

        return $this->prefix.str_pad($this->current_counter, 3, '0', STR_PAD_LEFT);
    }
}
