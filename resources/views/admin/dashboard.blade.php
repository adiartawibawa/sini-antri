<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-height-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-slate-900 text-white p-6">
            <div class="text-2xl font-bold mb-8 flex items-center gap-2">
                <span class="bg-blue-600 p-1 rounded">🎟️</span> AdminPanel
            </div>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded transition bg-blue-600">Dashboard</a>
                <a href="{{ route('admin.operators') }}" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Manajemen Operator</a>
                <a href="{{ route('admin.settings') }}" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Pengaturan Antrian</a>
                <a href="{{ route('admin.qrcode') }}" target="_blank" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Cetak QR Code</a>
            </nav>
            <div class="mt-auto pt-10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-slate-400 hover:text-white transition">Keluar</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight">Ringkasan Hari Ini</h1>
                <div class="text-slate-500 font-medium">{{ now()->translatedFormat('l, d F Y') }}</div>
            </header>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat Cards -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="text-slate-400 text-sm font-semibold uppercase mb-1">Total Antrian</div>
                    <div class="text-3xl font-bold">{{ $stats['total_today'] }}</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="text-blue-500 text-sm font-semibold uppercase mb-1">Menunggu</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['waiting'] }}</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="text-green-500 text-sm font-semibold uppercase mb-1">Selesai</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <div class="text-rose-500 text-sm font-semibold uppercase mb-1">Dilewati</div>
                    <div class="text-3xl font-bold text-rose-600">{{ $stats['skipped'] }}</div>
                </div>
            </div>

            <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold mb-4">Akses Cepat</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('display') }}" target="_blank" class="p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition border border-transparent hover:border-blue-200 group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition">📺</div>
                            <div class="font-bold">Layar Display</div>
                        </a>
                        <a href="{{ route('visitor.register') }}" target="_blank" class="p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition border border-transparent hover:border-blue-200 group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition">📱</div>
                            <div class="font-bold">Halaman Visitor</div>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
