<?php

namespace App\Events;

use App\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// =============================================
// Event: Pengunjung baru mengambil nomor antrian
// =============================================
class QueueCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Queue $queue) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('operator-dashboard'),  // Semua operator menerima
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'            => $this->queue->id,
            'uuid'          => $this->queue->uuid,
            'queue_number'  => $this->queue->queue_number,
            'queue_order'   => $this->queue->queue_order,
            'visitor_name'  => $this->queue->visitor_name,
            'purpose'       => $this->queue->purpose,
            'status'        => $this->queue->status,
            'created_at'    => $this->queue->created_at->format('H:i'),
            'waiting_count' => Queue::waiting()->count(),
        ];
    }
}
