<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class GenerateQueueVoices extends Command
{
    protected $signature = 'queue:generate-voices
                            {--max-angka=9   : Digit maksimum yang di-generate (0 s/d 9)}
                            {--max-loket=10  : Loket maksimum yang di-generate (1 s/d max)}';

    protected $description = 'Generate audio files for queue system using edge-tts';

    /** Digit 0-9 -> kata (untuk file angka/0.mp3 - angka/9.mp3) */
    private const DIGIT = [
        'nol', 'satu', 'dua', 'tiga', 'empat',
        'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
    ];

    /** Satuan 1-19 (untuk pembacaan loket natural) */
    private const SATUAN = [
        1 => 'satu', 'dua', 'tiga', 'empat', 'lima',
        'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',
        'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
        'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas',
    ];

    public function handle(): int
    {
        if (Process::run('edge-tts --version')->failed()) {
            $this->error('edge-tts tidak ditemukan. Install dengan: pip install edge-tts');

            return self::FAILURE;
        }

        $maxAngka = (int) $this->option('max-angka');
        $maxLoket = (int) $this->option('max-loket');

        if ($maxAngka < 0 || $maxAngka > 9) {
            $this->error('--max-angka harus antara 0 dan 9 (satu digit).');

            return self::FAILURE;
        }

        if ($maxLoket < 1) {
            $this->error('--max-loket harus minimal 1.');

            return self::FAILURE;
        }

        $this->info("Max angka : {$maxAngka}");
        $this->info("Max loket : {$maxLoket}");
        $this->newLine();

        $this->generateUmum();
        $this->generateHuruf();
        $this->generateAngka($maxAngka);
        $this->generateLoket($maxLoket);

        $this->newLine();
        $this->info('Audio generation completed!');

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Kelompok generate
    // -------------------------------------------------------------------------

    private function generateUmum(): void
    {
        $this->info('=== Umum ===');

        foreach ([
            'Perhatian' => 'umum/perhatian.mp3',
            'Nomor Antrian' => 'umum/nomor-antrian.mp3',
            'Silakan Menuju' => 'umum/silakan-menuju.mp3',
        ] as $text => $file) {
            $this->generate($text, $file);
        }
    }

    private function generateHuruf(): void
    {
        $this->newLine();
        $this->info('=== Huruf (A-Z) ===');

        foreach (range('a', 'z') as $char) {
            $this->generate(strtoupper($char), "huruf/{$char}.mp3");
        }
    }

    /**
     * Generate file digit 0-9.
     * File: angka/0.mp3 = "nol", angka/1.mp3 = "satu", dst.
     * Dipakai oleh QueueAudioService digit per digit saat membaca nomor antrian.
     */
    private function generateAngka(int $max): void
    {
        $this->newLine();
        $this->info("=== Angka digit (0 - {$max}) ===");

        for ($i = 0; $i <= $max; $i++) {
            $this->generate(self::DIGIT[$i], "angka/{$i}.mp3");
        }
    }

    /**
     * Generate file loket dengan pembacaan natural.
     * File: loket/loket-1.mp3 = "Loket satu", loket/loket-10.mp3 = "Loket sepuluh", dst.
     */
    private function generateLoket(int $max): void
    {
        $this->newLine();
        $this->info("=== Loket (1 - {$max}) ===");

        for ($i = 1; $i <= $max; $i++) {
            $this->generate('Loket '.$this->loketKeKata($i), "loket/loket-{$i}.mp3");
        }
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    /**
     * Konversi nomor loket ke kata natural Bahasa Indonesia.
     * Mendukung 1-99.
     * Contoh: 1 -> "satu" | 10 -> "sepuluh" | 12 -> "dua belas" | 20 -> "dua puluh"
     */
    protected function loketKeKata(int $n): string
    {
        if ($n >= 1 && $n <= 19) {
            return self::SATUAN[$n];
        }

        if ($n < 100) {
            $puluhan = [
                2 => 'dua puluh', 3 => 'tiga puluh', 4 => 'empat puluh', 5 => 'lima puluh',
                6 => 'enam puluh', 7 => 'tujuh puluh', 8 => 'delapan puluh', 9 => 'sembilan puluh',
            ];
            $p = (int) ($n / 10);
            $sisa = $n % 10;

            return $puluhan[$p].($sisa > 0 ? ' '.self::SATUAN[$sisa] : '');
        }

        // Fallback untuk loket > 99 (digit per digit)
        return implode(
            ' ',
            array_map(fn (string $d) => self::DIGIT[(int) $d], str_split((string) $n))
        );
    }

    protected function generate(string $text, string $filename): void
    {
        $path = storage_path("app/public/audio/{$filename}");
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $escaped = escapeshellarg($text);
        $command = "edge-tts --voice id-ID-GadisNeural --text {$escaped} --write-media \"{$path}\"";
        $result = Process::run($command);

        if ($result->successful()) {
            $this->line("  <info>✔</info> {$text} → {$filename}");
        } else {
            $this->line("  <error>✘</error> {$text} → {$filename}");
            $this->warn($result->errorOutput());
        }
    }
}
