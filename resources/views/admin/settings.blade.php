@extends('layouts.admin')

@section('title', 'Pengaturan Antrian')
@section('header_title', 'Konfigurasi Sistem')
@section('header_subtitle', 'Sesuaikan parameter operasional antrian')

@section('content')
    <div class="max-w-5xl">
        <!-- Main Settings Form -->
        <div class="bg-white rounded-md shadow-sm border border-[#e2e8f0] overflow-hidden">
            <div class="p-6 bg-[#fef2f2] border-b border-[#e2e8f0]">
                <h2 class="text-sm font-black text-[#b10303] uppercase tracking-widest flex items-center gap-3">
                    <span class="text-lg"><i class="fa-solid fa-screwdriver-wrench"></i></span> Parameter Dasar
                </h2>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label
                            class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                            <i class="fa-solid fa-tag mr-1"></i> Prefix Antrian
                        </label>
                        <input type="text" name="prefix" value="{{ $setting->prefix }}"
                            class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition font-mono"
                            maxlength="5">
                        <p class="text-[10px] text-[#64748b] font-bold mt-1.5 uppercase tracking-tighter">
                            <i class="fa-regular fa-circle-info mr-1"></i> Contoh: A, B, atau REG
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                            <i class="fa-regular fa-hourglass-half mr-1"></i> Rata-rata Layanan (Menit)
                        </label>
                        <input type="number" name="avg_service_minutes"
                            value="{{ $setting->avg_service_minutes }}"
                            class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                        <p class="text-[10px] text-[#64748b] font-bold mt-1.5 uppercase tracking-tighter">
                            Digunakan untuk estimasi tunggu visitor
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                            <i class="fa-solid fa-chart-simple mr-1"></i> Batas Antrian Per Hari
                        </label>
                        <input type="number" name="max_queue_limit" value="{{ $setting->max_queue_limit }}"
                            class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                        <p class="text-[10px] text-[#64748b] font-bold mt-1.5 uppercase tracking-tighter">
                            <i class="fa-regular fa-circle-info mr-1"></i> 0 = Tanpa batasan
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                            <i class="fa-brands fa-youtube mr-1"></i> YouTube URL (Layar Display)
                        </label>
                        <input type="text" name="youtube_url" value="{{ $setting->youtube_url }}"
                            class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition"
                            placeholder="https://www.youtube.com/watch?v=...">
                        <p class="text-[10px] text-[#64748b] font-bold mt-1.5 uppercase tracking-tighter">
                            Video yang diputar di monitor utama
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <div class="flex items-center gap-4 p-4 bg-[#fef2f2] rounded-md border border-[#e2e8f0]">
                        <input type="hidden" name="is_system_open" value="0">
                        <input type="checkbox" name="is_system_open" id="is_system_open" value="1"
                            {{ $setting->is_system_open ? 'checked' : '' }}
                            class="w-6 h-6 rounded text-[#b10303] focus:ring-0 transition cursor-pointer">
                        <div>
                            <label for="is_system_open"
                                class="font-bold text-[#1e293b] cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-door-open mr-1"></i> Sistem Dibuka
                            </label>
                            <p class="text-[9px] text-[#64748b] font-black uppercase tracking-widest">Izinkan
                                visitor ambil nomor</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 bg-[#fef2f2] rounded-md border border-[#e2e8f0]">
                        <input type="hidden" name="reset_daily" value="0">
                        <input type="checkbox" name="reset_daily" id="reset_daily" value="1"
                            {{ $setting->reset_daily ? 'checked' : '' }}
                            class="w-6 h-6 rounded text-[#b10303] focus:ring-0 transition cursor-pointer">
                        <div>
                            <label for="reset_daily"
                                class="font-bold text-[#1e293b] cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-arrows-rotate mr-1"></i> Auto Reset Harian
                            </label>
                            <p class="text-[9px] text-[#64748b] font-black uppercase tracking-widest">Reset
                                counter ke 1 setiap hari</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-[#e2e8f0] flex justify-end">
                    <button type="submit"
                        class="bg-[#b10303] text-white px-10 py-3 rounded-md font-bold hover:bg-[#8b0202] transition shadow-lg shadow-[#b10303]/20 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="mt-12 bg-white rounded-md shadow-sm border border-red-100 overflow-hidden">
            <div class="p-6 bg-red-50 border-b border-red-100">
                <h2 class="text-sm font-black text-rose-600 uppercase tracking-widest flex items-center gap-3">
                    <span class="text-lg"><i class="fa-solid fa-triangle-exclamation"></i></span> Zona
                    Berbahaya
                </h2>
            </div>
            <div class="p-8">
                <p class="text-sm text-[#64748b] font-medium mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-circle-exclamation"></i>
                    Tindakan di bawah ini akan mengatur ulang sistem secara permanen. Harap berhati-hati.
                </p>
                <form action="{{ route('admin.settings.reset') }}" method="POST"
                    onsubmit="return confirm('Reset counter ke 0? Semua nomor antrian berikutnya akan mulai dari 1.')">
                    @csrf
                    <button type="submit"
                        class="bg-rose-50 text-rose-600 border border-rose-200 px-6 py-3 rounded-md font-bold hover:bg-rose-600 hover:text-white transition-all uppercase text-xs tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-arrows-rotate"></i> Reset Counter Antrian ke 0
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
