<?php

namespace App\Console\Commands;

use App\Models\Queue;
use App\Models\QueueSetting;
use Illuminate\Console\Command;

class ResetDailyQueue extends Command
{
    protected $signature = 'queue:reset-daily';

    protected $description = 'Reset nomor antrian setiap hari (jalankan via scheduler jam 00:00)';

    public function handle(): void
    {
        $setting = QueueSetting::first();

        if (! $setting || ! $setting->reset_daily) {
            $this->info('Reset harian tidak aktif.');

            return;
        }

        // Tandai semua antrian hari sebelumnya yang masih waiting menjadi skipped
        $skipped = Queue::where('status', 'waiting')
            ->whereDate('created_at', '<', today())
            ->update(['status' => 'skipped']);

        // Reset counter
        $setting->update(['current_counter' => 0, 'last_reset_date' => today()]);

        $this->info("✅ Reset selesai. {$skipped} antrian leftover diskip.");
    }
}
