<?php

namespace App\Http\Controllers;

use App\Events\QueueCreated;
use App\Models\Queue;
use App\Models\QueueSetting;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function register(string $locationCode = 'umum')
    {
        $setting = QueueSetting::first();
        $waitingCount = Queue::waiting()->count();

        return view('visitor.register', compact('locationCode', 'setting', 'waitingCount'));
    }

    public function takeQueue(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:100',
            'purpose' => 'nullable|string|max:255',
        ]);

        $setting = QueueSetting::firstOrCreate([], [
            'prefix' => env('QUEUE_PREFIX', 'A'),
            'avg_service_minutes' => env('QUEUE_AVG_SERVICE_MINUTES', 5),
            'reset_daily' => true,
            'current_counter' => 0,
        ]);

        $queueNumber = $setting->generateNextNumber();
        $queueOrder = $setting->current_counter;

        $queue = Queue::create([
            'queue_number' => $queueNumber,
            'queue_order' => $queueOrder,
            'visitor_name' => $validated['visitor_name'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => 'waiting',
        ]);

        broadcast(new QueueCreated($queue))->toOthers();

        return redirect()->route('visitor.ticket', $queue->uuid);
    }

    public function ticket(string $uuid)
    {
        $queue = Queue::where('uuid', $uuid)->firstOrFail();
        $positionAhead = Queue::waiting()->where('queue_order', '<', $queue->queue_order)->count();
        $setting = QueueSetting::first();
        $estimatedMinutes = $positionAhead * ($setting?->avg_service_minutes ?? 5);

        return view('visitor.ticket', compact('queue', 'positionAhead', 'estimatedMinutes'));
    }

    // API: Posisi terkini (dipanggil oleh JS di HP pengunjung)
    public function ticketPosition(string $uuid)
    {
        $queue = Queue::where('uuid', $uuid)->firstOrFail();
        $positionAhead = Queue::waiting()->where('queue_order', '<', $queue->queue_order)->count();
        $setting = QueueSetting::first();

        return response()->json([
            'position_ahead' => $positionAhead,
            'estimated_minutes' => $positionAhead * ($setting?->avg_service_minutes ?? 5),
            'status' => $queue->status,
        ]);
    }
}
