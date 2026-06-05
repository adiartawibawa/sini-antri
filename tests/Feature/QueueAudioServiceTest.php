<?php

namespace Tests\Feature;

use App\Services\QueueAudioService;
use Tests\TestCase;

class QueueAudioServiceTest extends TestCase
{
    public function test_it_builds_playlist_digit_by_digit(): void
    {
        $service = new QueueAudioService();
        $playlist = $service->buildPlaylist('A-105', 'Loket 1');

        $this->assertContains(asset('storage/audio/huruf/a.mp3'), $playlist);
        $this->assertContains(asset('storage/audio/angka/1.mp3'), $playlist);
        $this->assertContains(asset('storage/audio/angka/0.mp3'), $playlist);
        $this->assertContains(asset('storage/audio/angka/5.mp3'), $playlist);
        
        // Ensure it doesn't contain the old format
        $this->assertNotContains(asset('storage/audio/angka/105.mp3'), $playlist);
    }

    public function test_it_preserves_leading_zeros(): void
    {
        $service = new QueueAudioService();
        $playlist = $service->buildPlaylist('B-007', 'Loket 2');

        $this->assertContains(asset('storage/audio/huruf/b.mp3'), $playlist);
        
        // Count occurrences of '0' audio
        $zeroCount = count(array_filter($playlist, fn($item) => str_contains($item, 'angka/0.mp3')));
        $this->assertEquals(2, $zeroCount);
        
        $this->assertContains(asset('storage/audio/angka/7.mp3'), $playlist);
    }
}
