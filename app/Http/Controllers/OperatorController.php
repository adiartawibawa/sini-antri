<?php

namespace App\Http\Controllers;

use App\Events\QueueCalled;
use App\Events\QueueStatusChanged;
use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    // Dashboard utama operator
    public function index()
    {
        $waitingQueues = Antrian::waiting()->get();
        $activeQueue = Antrian::where('operator_id', Auth::id())
            ->whereIn('status', ['called', 'serving'])
            ->latest('called_at')
            ->first();

        return view('operator.dashboard', compact('waitingQueues', 'activeQueue'));
    }

    // Panggil nomor antrian (ambil yang paling atas / FIFO)
    public function call(Request $request)
    {
        $operator = Auth::user();

        // Ambil antrian waiting teratas
        $antrian = Antrian::waiting()->first();

        if (! $antrian) {
            return response()->json(['message' => 'Tidak ada antrian yang menunggu.'], 404);
        }

        $antrian->update([
            'status' => 'called',
            'operator_id' => $operator->id,
            'called_at' => now(),
        ]);

        broadcast(new QueueCalled($antrian, $operator->loket_name));

        return response()->json([
            'message' => 'Berhasil memanggil antrian.',
            'queue_number' => $antrian->queue_number,
            'loket_name' => $operator->loket_name,
            'queue' => $antrian->load('operator'),
        ]);
    }

    // Panggil ulang (nomor yang sama dipanggil kembali)
    public function recall(Antrian $antrian)
    {
        $operator = Auth::user();

        if ($antrian->operator_id !== $operator->id) {
            return response()->json(['message' => 'Tidak memiliki akses ke antrian ini.'], 403);
        }

        $antrian->update(['called_at' => now(), 'status' => 'called']);

        broadcast(new QueueCalled($antrian, $operator->loket_name));

        return response()->json(['message' => 'Panggilan ulang berhasil.', 'queue' => $antrian]);
    }

    // Lewati / skip nomor yang tidak hadir
    public function skip(Antrian $antrian)
    {
        $operator = Auth::user();

        if ($antrian->operator_id !== $operator->id) {
            return response()->json(['message' => 'Tidak memiliki akses ke antrian ini.'], 403);
        }

        $previousStatus = $antrian->status;

        $antrian->update([
            'status' => 'skipped',
        ]);

        broadcast(new QueueStatusChanged($antrian, $previousStatus));

        return response()->json([
            'message' => 'Antrian dilewati.',
            'waiting_count' => Antrian::waiting()->count(),
        ]);
    }

    // Selesaikan sesi layanan
    public function complete(Antrian $antrian)
    {
        $operator = Auth::user();

        if ($antrian->operator_id !== $operator->id) {
            return response()->json(['message' => 'Tidak memiliki akses ke antrian ini.'], 403);
        }

        $previousStatus = $antrian->status;

        $antrian->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        broadcast(new QueueStatusChanged($antrian, $previousStatus));

        return response()->json([
            'message' => 'Antrian selesai dilayani.',
            'waiting_count' => Antrian::waiting()->count(),
        ]);
    }

    // API: Ambil daftar antrian menunggu (untuk polling fallback)
    public function waitingList()
    {
        return response()->json([
            'queues' => Antrian::waiting()->get(),
            'waiting_count' => Antrian::waiting()->count(),
        ]);
    }
}
