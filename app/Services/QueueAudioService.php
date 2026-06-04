<?php

namespace App\Services;

class QueueAudioService
{
    protected array $angka = [
        '0' => 'nol',
        '1' => 'satu',
        '2' => 'dua',
        '3' => 'tiga',
        '4' => 'empat',
        '5' => 'lima',
        '6' => 'enam',
        '7' => 'tujuh',
        '8' => 'delapan',
        '9' => 'sembilan',
    ];

    public function buildPlaylist(string $queueNumber, string $loket): array
    {
        $playlist = [
            asset('storage/audio/umum/perhatian.mp3'),
            asset('storage/audio/umum/nomor-antrian.mp3'),
        ];

        foreach (str_split(strtoupper($queueNumber)) as $char) {
            if (ctype_alpha($char)) {
                $playlist[] = asset("storage/audio/huruf/" . strtolower($char) . ".mp3");
            }

            if (isset($this->angka[$char])) {
                $playlist[] = asset("storage/audio/angka/" . $this->angka[$char] . ".mp3");
            }
        }

        $playlist[] = asset('storage/audio/umum/silakan-menuju.mp3');

        $playlist[] = asset(
            'storage/audio/loket/' . 
            strtolower(str_replace(' ', '-', $loket)) . 
            '.mp3'
        );

        return $playlist;
    }
}
