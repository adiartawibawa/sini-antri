@extends('layouts.display')

@section('title', 'QR Code Antrian')

@section('body_class', 'bg-[#fef2f2] min-h-screen flex items-center justify-center p-6 font-sans')

@push('styles')
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none;
            }
        }
    </style>
    <script>
        // Override tailwind config for specific colors if needed, 
        // but it's better to keep it consistent with the layout.
    </script>
@endpush

@section('content')
    <div class="text-center">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200 p-8 md:p-12 inline-block min-w-[340px] max-w-md">

            <!-- Header -->
            <div class="mb-6">
                <div
                    class="w-16 h-16 bg-[#b10303] rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-[#b10303]/20">
                    <i class="fa-solid fa-qrcode text-3xl text-white"></i>
                </div>
                <h1 class="text-xl font-extrabold text-[#1e293b]">Ambil Nomor Antrian</h1>
                <p class="text-sm text-[#64748b] mt-1">
                    <i class="fa-solid fa-camera mr-1"></i> Pindai QR Code di bawah dengan kamera HP Anda
                </p>
            </div>

            <!-- QR Code Wrapper -->
            <div class="inline-block p-4 rounded-xl bg-white shadow-md border-4 border-[#b10303]">
                {!! QrCode::size(220)->style('round')->eye('circle')->color(177, 3, 3)->generate($url) !!}
            </div>

            <!-- Location Info -->
            <div class="mt-6 flex items-center justify-center gap-2">
                <i class="fa-solid fa-location-dot text-[#b10303] text-sm"></i>
                <span class="text-xs text-[#64748b]">Lokasi:</span>
                <strong class="text-sm font-bold text-[#1e293b] bg-[#fef2f2] px-3 py-1 rounded-full">
                    {{ strtoupper($locationCode) }}
                </strong>
            </div>

            <!-- URL Box -->
            <div class="mt-4 bg-[#fef2f2] border border-[#e2e8f0] rounded-xl p-3">
                <div class="text-[10px] text-[#64748b] font-bold uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-link mr-1"></i> URL Antrian
                </div>
                <code class="text-xs text-[#b10303] font-mono break-all">{{ $url }}</code>
            </div>

            <!-- Print Button -->
            <button onclick="window.print()"
                class="no-print mt-6 bg-[#b10303] hover:bg-[#8b0202] text-white font-bold py-3 px-6 rounded-xl transition active:scale-95 shadow-lg shadow-[#b10303]/20 flex items-center justify-center gap-2 w-full">
                <i class="fa-solid fa-print"></i> Cetak QR Code
            </button>

            <!-- Footer Note -->
            <p
                class="text-[10px] text-[#64748b] font-bold uppercase tracking-widest mt-6 pt-4 border-t border-[#e2e8f0]">
                <i class="fa-solid fa-ticket mr-1"></i> Sini <span class="text-[#b10303]">Antri</span> - Digital Queue
                System v1.0
            </p>
            <p class="text-[10px] text-slate-500 font-light tracking-widest pt-2">
                Made with ❤️ Adi Arta Wibawa</p>
        </div>
    </div>
@endsection
