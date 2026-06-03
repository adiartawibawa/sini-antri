<?php

namespace App\Events;

use App\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// =============================================
// Event: Operator memanggil nomor antrian
// Broadcast ke: Display TV + HP pengunjung ybs
// =============================================
class QueueCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Queue $queue, public string $loketName) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('display-screen'),              // Layar TV/Monitor utama
            new Channel('ticket.' . $this->queue->uuid), // HP pengunjung spesifik
            new Channel('operator-dashboard'),           // Update dashboard semua operator
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id'            => $this->queue->id,
            'uuid'          => $this->queue->uuid,
            'queue_number'  => $this->queue->queue_number,
            'visitor_name'  => $this->queue->visitor_name,
            'loket_name'    => $this->loketName,
            'status'        => $this->queue->status,
            'called_at'     => $this->queue->called_at?->format('H:i:s'),
        ];
    }
}
