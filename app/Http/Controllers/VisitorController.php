<?php

namespace App\Http\Controllers;

use App\Events\QueueCreated;
use App\Models\Antrian;
use App\Models\QueueSetting;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function register(string $locationCode = 'umum')
    {
        $setting = QueueSetting::first();
        $waitingCount = Antrian::waiting()->count();

        // Cek jika sistem tutup
        if ($setting && ! $setting->is_system_open) {
            return view('visitor.closed', compact('setting'));
        }

        // Cek jika kuota penuh (0 = unlimited)
        if ($setting && $setting->max_queue_limit > 0 && $setting->current_counter >= $setting->max_queue_limit) {
            return view('visitor.full', compact('setting'));
        }

        return view('visitor.register', compact('locationCode', 'setting', 'waitingCount'));
    }

    public function takeQueue(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:100',
            'purpose' => 'nullable|string|max:255',
        ]);

        $setting = QueueSetting::first();

        if (! $setting || ! $setting->is_system_open) {
            return back()->withErrors(['visitor_name' => 'Maaf, pendaftaran antrian sedang ditutup.']);
        }

        if ($setting->max_queue_limit > 0 && $setting->current_counter >= $setting->max_queue_limit) {
            return back()->withErrors(['visitor_name' => 'Maaf, kuota antrian hari ini telah penuh.']);
        }

        $queueNumber = $setting->generateNextNumber();
        $queueOrder = $setting->current_counter;

        $antrian = Antrian::create([
            'queue_number' => $queueNumber,
            'queue_order' => $queueOrder,
            'visitor_name' => $validated['visitor_name'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => 'waiting',
        ]);

        broadcast(new QueueCreated($antrian))->toOthers();

        return redirect()->route('visitor.ticket', $antrian->uuid);
    }

    public function ticket(string $uuid)
    {
        $antrian = Antrian::where('uuid', $uuid)->firstOrFail();
        $positionAhead = $antrian->position_ahead;
        $setting = QueueSetting::first();
        $estimatedMinutes = $antrian->estimated_wait_minutes;

        return view('visitor.ticket', [
            'queue' => $antrian,
            'positionAhead' => $positionAhead,
            'estimatedMinutes' => $estimatedMinutes,
        ]);
    }

    // API: Posisi terkini (dipanggil oleh JS di HP pengunjung)
    public function ticketPosition(string $uuid)
    {
        $antrian = Antrian::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'position_ahead' => $antrian->position_ahead,
            'estimated_minutes' => $antrian->estimated_wait_minutes,
            'status' => $antrian->status,
        ]);
    }
}
