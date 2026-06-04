<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class GenerateQueueVoices extends Command
{
    protected $signature = 'queue:generate-voices
                            {--max-angka=999 : Angka maksimum yang di-generate (0 s/d max)}
                            {--max-loket=10  : Loket maksimum yang di-generate (1 s/d max)}';

    protected $description = 'Generate audio files for queue system using edge-tts';

    public function handle()
    {
        $checkProcess = Process::run('edge-tts --version');
        if ($checkProcess->failed()) {
            $this->error('Error: edge-tts is not installed or not in PATH.');
            $this->info('Please install it using: pip install edge-tts');

            return 1;
        }

        $maxAngka = (int) $this->option('max-angka');
        $maxLoket = (int) $this->option('max-loket');

        if ($maxAngka < 0 || $maxAngka > 999) {
            $this->error('--max-angka harus antara 0 dan 999.');

            return 1;
        }
        if ($maxLoket < 1) {
            $this->error('--max-loket harus minimal 1.');

            return 1;
        }

        $this->info("Max angka : {$maxAngka}");
        $this->info("Max loket : {$maxLoket}");
        $this->info('Starting audio generation...');

        // 1. Umum
        $this->generate('Perhatian', 'umum/perhatian.mp3');
        $this->generate('Nomor Antrian', 'umum/nomor-antrian.mp3');
        $this->generate('Silakan Menuju', 'umum/silakan-menuju.mp3');

        // 2. Huruf (A-Z)
        foreach (range('a', 'z') as $char) {
            $this->generate(strtoupper($char), "huruf/{$char}.mp3");
        }

        // 3. Angka (0 s/d maxAngka)
        for ($i = 0; $i <= $maxAngka; $i++) {
            $text = $this->angkaKeKata($i);
            $this->generate($text, "angka/{$i}.mp3");
        }

        // 4. Loket (1 s/d maxLoket)
        for ($i = 1; $i <= $maxLoket; $i++) {
            $loketText = 'Loket '.$this->angkaKeKata($i);
            $this->generate($loketText, "loket/loket-{$i}.mp3");
        }

        $this->info('Audio generation completed!');

        return 0;
    }

    /**
     * Konversi angka (0-999) ke kata dalam Bahasa Indonesia.
     */
    protected function angkaKeKata(int $n): string
    {
        $satuan = [
            'nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
            'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas',
        ];

        if ($n < 20) {
            return $satuan[$n];
        }

        if ($n < 100) {
            $puluhan = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh',
                'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
            $sisa = $n % 10;

            return $puluhan[(int) ($n / 10)].($sisa > 0 ? ' '.$satuan[$sisa] : '');
        }

        // 100-999
        $ratus = (int) ($n / 100);
        $sisa = $n % 100;
        $ratusText = ($ratus === 1 ? 'seratus' : $satuan[$ratus].' ratus');

        return $sisa === 0 ? $ratusText : $ratusText.' '.$this->angkaKeKata($sisa);
    }

    protected function generate(string $text, string $filename): void
    {
        $path = storage_path("app/public/audio/{$filename}");
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $command = "edge-tts --voice id-ID-GadisNeural --text \"{$text}\" --write-media \"{$path}\"";
        $result = Process::run($command);

        if ($result->successful()) {
            $this->info("Generated: {$text} -> {$filename}");
        } else {
            $this->error("Failed: {$text} -> {$filename}");
            $this->error($result->errorOutput());
        }
    }
}
