<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class DevCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:dev';

    /**
     * The console command description.
     */
    protected $description = 'Start Reverb and Queue Worker';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $appUrl = config('app.url');

        $this->info('Starting Sini Antri Development Environment...');
        $this->info("APP_URL: {$appUrl}");

        $this->line('');
        $this->comment('1. Starting Reverb Server...');
        $this->comment('2. Starting Queue Worker...');
        $this->line('');

        $useServe = env('APP_USE_SERVE', false);

        $pool = Process::pool(function ($pool) use ($useServe) {

            if ($useServe) {
                $pool->as('serve')
                    ->path(base_path())
                    ->timeout(0)
                    ->command('php artisan serve');
            }

            $pool->as('reverb')
                ->path(base_path())
                ->timeout(0)
                ->command('php artisan reverb:start');

            $pool->as('queue')
                ->path(base_path())
                ->timeout(0)
                ->command('php artisan queue:work');
        })->start(function (string $type, string $output, string $name) {
            $prefix = Str::upper($name);

            $color = match ($name) {
                'reverb' => 'comment',
                'queue' => 'question',
                default => 'line',
            };

            $this->{$color}("[{$prefix}] ".trim($output));
        });

        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);

            pcntl_signal(SIGINT, function () use ($pool) {
                $this->warn("\nStopping all processes...");

                $pool->running()->each->signal(SIGINT);

                exit;
            });
        }

        $pool->wait();
    }
}
