<?php

namespace App\Events;

use App\Models\Antrian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// =============================================
// Event: Status antrian berubah (skip/complete/dll)
// =============================================
class QueueStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Antrian $antrian,
        public string $previousStatus
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('ticket.'.$this->antrian->uuid), // HP pengunjung
            new Channel('operator-dashboard'),            // Dashboard operator
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->antrian->id,
            'uuid' => $this->antrian->uuid,
            'queue_number' => $this->antrian->queue_number,
            'status' => $this->antrian->status,
            'previous_status' => $this->previousStatus,
            'waiting_count' => Antrian::waiting()->count(),
        ];
    }
}
