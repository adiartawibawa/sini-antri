@extends('layouts.visitor')

@section('title', 'Kuota Penuh')

@section('content')
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-[#e2e8f0] text-center">
        <div class="text-6xl mb-6">
            <i class="fa-solid fa-circle-exclamation text-[#b10303]"></i>
        </div>
        <h1 class="text-2xl font-black text-[#1e293b] mb-3">Kuota Sudah Penuh</h1>
        <p class="text-[#64748b] leading-relaxed">Maaf, batas maksimal antrian untuk hari ini telah tercapai (Batas:
            {{ $setting->max_queue_limit }}). Silakan hubungi petugas atau kembali lagi besok.</p>
        <div class="mt-8 pt-8 border-t border-[#e2e8f0]">
            <p class="text-xs text-[#64748b] font-bold uppercase tracking-widest">
                <i class="fa-solid fa-ticket-alt mr-1"></i> Sistem Antrian Digital
            </p>
        </div>
    </div>
@endsection
