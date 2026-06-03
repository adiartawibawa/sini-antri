<?php

namespace App\Events;

use App\Models\Queue;
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
        public Queue  $queue,
        public string $previousStatus
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('ticket.' . $this->queue->uuid), // HP pengunjung
            new Channel('operator-dashboard'),            // Dashboard operator
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->queue->id,
            'uuid'            => $this->queue->uuid,
            'queue_number'    => $this->queue->queue_number,
            'status'          => $this->queue->status,
            'previous_status' => $this->previousStatus,
            'waiting_count'   => Queue::waiting()->count(),
        ];
    }
}
