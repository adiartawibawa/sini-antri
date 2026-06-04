<?php

namespace App\Events;

use App\Models\Antrian;
use App\Services\QueueAudioService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Antrian $antrian, public string $loketName) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('display-screen'),
            new Channel('ticket.'.$this->antrian->uuid),
            new Channel('operator-dashboard'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->antrian->id,
            'uuid' => $this->antrian->uuid,
            'queue_number' => $this->antrian->queue_number,
            'visitor_name' => $this->antrian->visitor_name,
            'loket_name' => $this->loketName,
            'status' => $this->antrian->status,
            'called_at' => $this->antrian->called_at?->format('H:i:s'),
            'audio_playlist' => app(QueueAudioService::class)->buildPlaylist(
                $this->antrian->queue_number,
                $this->loketName
            ),
        ];
    }
}
