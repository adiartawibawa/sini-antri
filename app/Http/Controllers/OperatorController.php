<?php

namespace App\Http\Controllers;

use App\Events\QueueCalled;
use App\Events\QueueStatusChanged;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:operator');
    }

    // Dashboard utama operator
    public function index()
    {
        $waitingQueues = Queue::waiting()->get();
        $activeQueue = Queue::where('operator_id', Auth::id())
            ->whereIn('status', ['called', 'serving'])
            ->latest('called_at')
            ->first();

        return view('operator.dashboard', compact('waitingQueues', 'activeQueue'));
    }

    // Panggil nomor antrian (ambil yang paling atas / FIFO)
    public function call(Request $request)
    {
        $operator = Auth::guard('operator')->user();

        // Ambil antrian waiting teratas
        $queue = Queue::waiting()->first();

        if (! $queue) {
            return response()->json(['message' => 'Tidak ada antrian yang menunggu.'], 404);
        }

        $previousStatus = $queue->status;

        $queue->update([
            'status' => 'called',
            'operator_id' => $operator->id,
            'called_at' => now(),
        ]);

        broadcast(new QueueCalled($queue, $operator->loket_name));

        return response()->json([
            'message' => 'Berhasil memanggil antrian.',
            'queue_number' => $queue->queue_number,
            'loket_name' => $operator->loket_name,
            'queue' => $queue->load('operator'),
        ]);
    }

    // Panggil ulang (nomor yang sama dipanggil kembali)
    public function recall(Queue $queue)
    {
        $operator = Auth::guard('operator')->user();

        if ($queue->operator_id !== $operator->id) {
            return response()->json(['message' => 'Tidak memiliki akses ke antrian ini.'], 403);
        }

        $queue->update(['called_at' => now(), 'status' => 'called']);

        broadcast(new QueueCalled($queue, $operator->loket_name));

        return response()->json(['message' => 'Panggilan ulang berhasil.', 'queue' => $queue]);
    }

    // Lewati / skip nomor yang tidak hadir
    public function skip(Queue $queue)
    {
        $operator = Auth::guard('operator')->user();
        $previousStatus = $queue->status;

        $queue->update([
            'status' => 'skipped',
            'operator_id' => $operator->id,
        ]);

        broadcast(new QueueStatusChanged($queue, $previousStatus));

        return response()->json([
            'message' => 'Antrian dilewati.',
            'waiting_count' => Queue::waiting()->count(),
        ]);
    }

    // Selesaikan sesi layanan
    public function complete(Queue $queue)
    {
        $operator = Auth::guard('operator')->user();
        $previousStatus = $queue->status;

        $queue->update([
            'status' => 'completed',
            'operator_id' => $operator->id,
            'completed_at' => now(),
        ]);

        broadcast(new QueueStatusChanged($queue, $previousStatus));

        return response()->json([
            'message' => 'Antrian selesai dilayani.',
            'waiting_count' => Queue::waiting()->count(),
        ]);
    }

    // API: Ambil daftar antrian menunggu (untuk polling fallback)
    public function waitingList()
    {
        return response()->json([
            'queues' => Queue::waiting()->get(),
            'waiting_count' => Queue::waiting()->count(),
        ]);
    }
}
