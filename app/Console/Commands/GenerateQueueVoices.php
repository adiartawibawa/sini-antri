<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class GenerateQueueVoices extends Command
{
    protected $signature = 'queue:generate-voices';
    protected $description = 'Generate audio files for queue system using edge-tts';

    public function handle()
    {
        $this->info('Starting audio generation...');

        // 1. Umum
        $this->generate('Perhatian', 'umum/perhatian.mp3');
        $this->generate('Nomor Antrian', 'umum/nomor-antrian.mp3');
        $this->generate('Silakan Menuju', 'umum/silakan-menuju.mp3');

        // 2. Huruf (A-Z)
        foreach (range('a', 'z') as $char) {
            $this->generate(strtoupper($char), "huruf/{$char}.mp3");
        }

        // 3. Angka (0-9)
        $angka = [
            '0' => 'nol', '1' => 'satu', '2' => 'dua', '3' => 'tiga', '4' => 'empat',
            '5' => 'lima', '6' => 'enam', '7' => 'tujuh', '8' => 'delapan', '9' => 'sembilan'
        ];
        foreach ($angka as $num => $text) {
            $this->generate($text, "angka/{$text}.mp3");
        }

        // 4. Loket (1-10)
        for ($i = 1; $i <= 10; $i++) {
            $this->generate("Loket {$i}", "loket/loket-{$i}.mp3");
        }

        $this->info('Audio generation completed!');
    }

    protected function generate($text, $filename)
    {
        $path = storage_path("app/public/audio/{$filename}");
        
        $this->info("Generating: {$text} -> {$filename}");
        
        // Ensure directory exists
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Command: edge-tts --voice id-ID-GadisNeural --text "Text" --write-media path
        $command = "edge-tts --voice id-ID-GadisNeural --text \"{$text}\" --write-media \"{$path}\"";
        
        Process::run($command);
    }
}
