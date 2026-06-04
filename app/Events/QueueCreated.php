<?php

namespace App\Events;

use App\Models\Antrian;
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

    public function __construct(public Antrian $antrian) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('operator-dashboard'),  // Semua operator menerima
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->antrian->id,
            'uuid' => $this->antrian->uuid,
            'queue_number' => $this->antrian->queue_number,
            'queue_order' => $this->antrian->queue_order,
            'visitor_name' => $this->antrian->visitor_name,
            'purpose' => $this->antrian->purpose,
            'status' => $this->antrian->status,
            'created_at' => $this->antrian->created_at->format('H:i'),
            'waiting_count' => Antrian::waiting()->count(),
        ];
    }
}
