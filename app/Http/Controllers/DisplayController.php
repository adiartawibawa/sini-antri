<?php

namespace App\Http\Controllers;

use App\Models\Antrian;

class DisplayController extends Controller
{
    // Layar display utama (TV/Monitor di ruang tunggu)
    public function index()
    {
        // Ambil nomor yang terakhir dipanggil
        $currentQueue = Antrian::whereIn('status', ['called', 'serving'])
            ->latest('called_at')
            ->first();

        // 5 antrian berikutnya
        $nextQueues = Antrian::waiting()->take(5)->get();

        return view('display.screen', compact('currentQueue', 'nextQueues'));
    }

    // API: status antrian terkini untuk display
    public function status()
    {
        $currentQueue = Antrian::whereIn('status', ['called', 'serving'])
            ->latest('called_at')
            ->first();

        $nextQueues = Antrian::waiting()->take(5)->get();

        return response()->json([
            'current' => $currentQueue ? [
                'queue_number' => $currentQueue->queue_number,
                'visitor_name' => $currentQueue->visitor_name,
                'loket_name' => $currentQueue->operator?->loket_name,
            ] : null,
            'next' => $nextQueues->map(fn ($q) => [
                'queue_number' => $q->queue_number,
                'visitor_name' => $q->visitor_name,
            ]),
        ]);
    }

    // QR Code Generator untuk lokasi tertentu
    public function qrcode(string $locationCode = 'umum')
    {
        $url = route('visitor.register', $locationCode);

        return view('display.qrcode', compact('url', 'locationCode'));
    }
}
