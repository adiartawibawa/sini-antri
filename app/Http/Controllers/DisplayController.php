<?php

namespace App\Http\Controllers;

use App\Models\Queue;

class DisplayController extends Controller
{
    // Layar display utama (TV/Monitor di ruang tunggu)
    public function index()
    {
        // Ambil nomor yang terakhir dipanggil
        $currentQueue = Queue::whereIn('status', ['called', 'serving'])
            ->latest('called_at')
            ->first();

        // 5 antrian berikutnya
        $nextQueues = Queue::waiting()->take(5)->get();

        return view('display.screen', compact('currentQueue', 'nextQueues'));
    }

    // API: status antrian terkini untuk display
    public function status()
    {
        $currentQueue = Queue::whereIn('status', ['called', 'serving'])
            ->latest('called_at')
            ->first();

        $nextQueues = Queue::waiting()->take(5)->get();

        return response()->json([
            'current' => $currentQueue?->only(['queue_number', 'visitor_name', 'operator.loket_name']),
            'next' => $nextQueues->map(fn ($q) => $q->only(['queue_number', 'visitor_name'])),
        ]);
    }

    // QR Code Generator untuk lokasi tertentu
    public function qrcode(string $locationCode = 'umum')
    {
        $url = route('visitor.register', $locationCode);

        return view('display.qrcode', compact('url', 'locationCode'));
    }
}
