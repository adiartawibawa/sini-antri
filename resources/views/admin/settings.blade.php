<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Antrian - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-height-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-slate-900 text-white p-6">
            <div class="text-2xl font-bold mb-8 flex items-center gap-2">
                <span class="bg-blue-600 p-1 rounded">🎟️</span> AdminPanel
            </div>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Dashboard</a>
                <a href="{{ route('admin.operators') }}" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Manajemen Operator</a>
                <a href="{{ route('admin.settings') }}" class="block px-4 py-2.5 rounded transition bg-blue-600">Pengaturan Antrian</a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-8 max-w-4xl">
            <h1 class="text-3xl font-extrabold mb-8">Konfigurasi Sistem</h1>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold mb-2">Prefix Antrian</label>
                            <input type="text" name="prefix" value="{{ $setting->prefix }}" class="w-full p-3 border rounded-xl bg-slate-50" maxlength="5">
                            <p class="text-xs text-slate-400 mt-1">Contoh: A, B, atau REG</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Rata-rata Layanan (Menit)</label>
                            <input type="number" name="avg_service_minutes" value="{{ $setting->avg_service_minutes }}" class="w-full p-3 border rounded-xl bg-slate-50">
                            <p class="text-xs text-slate-400 mt-1">Digunakan untuk estimasi tunggu visitor</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Batas Antrian Per Hari</label>
                            <input type="number" name="max_queue_limit" value="{{ $setting->max_queue_limit }}" class="w-full p-3 border rounded-xl bg-slate-50">
                            <p class="text-xs text-slate-400 mt-1">0 = Tidak ada batasan</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Status Sistem</label>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border">
                                <input type="hidden" name="is_system_open" value="0">
                                <input type="checkbox" name="is_system_open" id="is_system_open" value="1" {{ $setting->is_system_open ? 'checked' : '' }} class="w-5 h-5 rounded text-blue-600">
                                <label for="is_system_open" class="font-semibold text-sm">Sistem Dibuka (Menerima Antrian)</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-4 border-t">
                        <input type="hidden" name="reset_daily" value="0">
                        <input type="checkbox" name="reset_daily" id="reset_daily" value="1" {{ $setting->reset_daily ? 'checked' : '' }} class="w-5 h-5 rounded text-blue-600">
                        <label for="reset_daily" class="font-semibold">Reset Nomor Antrian Setiap Hari Secara Otomatis</label>
                    </div>

                    <div class="pt-6 border-t flex justify-between items-center">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <div class="mt-12 bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                <h2 class="text-xl font-bold text-rose-600 mb-2">Zona Berbahaya</h2>
                <p class="text-slate-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                
                <form action="{{ route('admin.settings.reset') }}" method="POST" onsubmit="return confirm('Reset counter ke 0? Semua nomor antrian berikutnya akan mulai dari 1.')">
                    @csrf
                    <button type="submit" class="bg-rose-50 text-rose-600 border border-rose-200 px-6 py-3 rounded-xl font-bold hover:bg-rose-600 hover:text-white transition">Reset Counter Antrian ke 0</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
