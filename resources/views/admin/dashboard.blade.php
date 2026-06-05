<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-link.active {
            background-color: #b10303;
            color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</head>

<body class="bg-[#fef2f2] text-[#1e293b]">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-72 bg-[#1e293b] text-white flex flex-col shadow-xl z-20">
            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-[#b10303] rounded-md flex items-center justify-center text-xl shadow-lg shadow-[#b10303]/20">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tight uppercase">Sini <span
                                class="text-[#b10303]">Antri</span></h2>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Admin Control</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-md font-semibold transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-chart-line"></i></span> Dashboard
                </a>
                <a href="{{ route('admin.operators') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-users"></i></span> Operator
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-gear"></i></span> Pengaturan
                </a>
                <a href="{{ route('admin.qrcode') }}" target="_blank"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-print"></i></span> Cetak QR
                </a>
            </nav>

            <div class="p-4 mt-auto border-t border-white/5">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-rose-400 hover:bg-rose-500/10 transition">
                        <span class="text-lg w-8 text-center"><i class="fa-solid fa-right-from-bracket"></i></span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header
                class="h-20 bg-white border-b border-[#e2e8f0] px-8 flex items-center justify-between sticky top-0 z-10">
                <div>
                    <h1 class="text-xl font-bold text-[#1e293b]">Ringkasan Dashboard</h1>
                    <p class="text-sm text-[#64748b] font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex flex-col text-right hidden sm:block">
                        <span class="text-sm font-bold text-[#1e293b]">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-[#64748b] font-bold uppercase tracking-wider">Administrator</span>
                    </div>
                    <div
                        class="w-10 h-10 rounded-full bg-[#fef2f2] flex items-center justify-center text-lg shadow-inner">
                        <i class="fa-solid fa-user text-[#b10303]"></i>
                    </div>
                </div>
            </header>

            <div class="p-8 space-y-8">
                @if (session('success'))
                    <div
                        class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-md shadow-sm flex items-center gap-3 animate-fade-in">
                        <span class="text-xl"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="bg-white p-6 rounded-md shadow-sm border border-[#e2e8f0] relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-[#fef2f2] rounded-md flex items-center justify-center text-2xl group-hover:scale-110 transition-transform text-[#b10303]">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                            <span class="text-[10px] font-black text-[#64748b] uppercase tracking-widest">Hari
                                Ini</span>
                        </div>
                        <div class="text-3xl font-black text-[#1e293b]">{{ $stats['total_today'] }}</div>
                        <div class="text-xs text-[#64748b] font-bold mt-1 uppercase">Total Antrian</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-[#b10303] w-full"></div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-md shadow-sm border border-[#e2e8f0] relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-[#fef2f2] rounded-md flex items-center justify-center text-2xl group-hover:scale-110 transition-transform text-[#b10303]">
                                <i class="fa-solid fa-hourglass-start"></i>
                            </div>
                            <span class="text-[10px] font-black text-[#b10303] uppercase tracking-widest">Antri</span>
                        </div>
                        <div class="text-3xl font-black text-[#b10303]">{{ $stats['waiting'] }}</div>
                        <div class="text-xs text-[#64748b] font-bold mt-1 uppercase">Menunggu Pelayanan</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-[#b10303] w-full opacity-50"></div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-md shadow-sm border border-[#e2e8f0] relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-emerald-50 rounded-md flex items-center justify-center text-2xl group-hover:scale-110 transition-transform text-emerald-500">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <span
                                class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Selesai</span>
                        </div>
                        <div class="text-3xl font-black text-emerald-600">{{ $stats['completed'] }}</div>
                        <div class="text-xs text-[#64748b] font-bold mt-1 uppercase">Telah Dilayani</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 w-full"></div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-md shadow-sm border border-[#e2e8f0] relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-amber-50 rounded-md flex items-center justify-center text-2xl group-hover:scale-110 transition-transform text-amber-500">
                                <i class="fa-solid fa-forward-step"></i>
                            </div>
                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Skip</span>
                        </div>
                        <div class="text-3xl font-black text-amber-600">{{ $stats['skipped'] }}</div>
                        <div class="text-xs text-[#64748b] font-bold mt-1 uppercase">Antrian Dilewati</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-amber-500 w-full"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Recent Activity -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="flex items-center justify-between px-2">
                            <h2 class="text-lg font-bold text-[#1e293b]">
                                <i class="fa-regular fa-clock mr-2"></i> Aktivitas Terbaru
                            </h2>
                            <a href="#"
                                class="text-xs font-bold text-[#b10303] hover:underline uppercase tracking-wider">
                                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="bg-white rounded-md shadow-sm border border-[#e2e8f0] overflow-hidden">
                            <table class="w-full text-left">
                                <thead class="bg-[#fef2f2] border-b border-[#e2e8f0]">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                                            Nomor</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                                            Visitor</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                                            Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e2e8f0]">
                                    @forelse($recentQueues as $q)
                                        <tr class="hover:bg-[#fef2f2] transition-colors">
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 py-1 bg-[#fef2f2] rounded text-sm font-bold text-[#b10303]">{{ $q->queue_number }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-[#1e293b]">{{ $q->visitor_name }}</div>
                                                <div
                                                    class="text-[10px] text-[#64748b] font-bold uppercase tracking-tight">
                                                    {{ $q->operator?->loket_name ?? 'Menunggu' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusColors = [
                                                        'waiting' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                        'called' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                        'serving' =>
                                                            'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                        'completed' => 'bg-slate-50 text-slate-600 border-slate-100',
                                                        'skipped' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                    ];
                                                @endphp
                                                <span
                                                    class="px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $statusColors[$q->status] ?? 'bg-slate-50' }}">
                                                    @if ($q->status === 'waiting')
                                                        <i class="fa-regular fa-clock mr-1"></i>
                                                    @elseif($q->status === 'called')
                                                        <i class="fa-solid fa-bullhorn mr-1"></i>
                                                    @elseif($q->status === 'serving')
                                                        <i class="fa-regular fa-circle-check mr-1"></i>
                                                    @elseif($q->status === 'completed')
                                                        <i class="fa-regular fa-flag-checkered mr-1"></i>
                                                    @elseif($q->status === 'skipped')
                                                        <i class="fa-solid fa-forward-step mr-1"></i>
                                                    @endif
                                                    {{ $q->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs font-bold text-[#64748b]">
                                                {{ $q->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-12 text-center text-[#64748b] italic font-medium">
                                                <i class="fa-regular fa-face-smile text-2xl block mb-2"></i>
                                                Belum ada aktivitas hari ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="space-y-8">
                        <!-- Quick Links -->
                        <div class="space-y-4">
                            <h2 class="text-lg font-bold text-[#1e293b] px-2">
                                <i class="fa-solid fa-bolt mr-2"></i> Akses Cepat
                            </h2>
                            <div class="grid grid-cols-1 gap-3">
                                <a href="{{ route('display') }}" target="_blank"
                                    class="p-4 bg-white rounded-md border border-[#e2e8f0] shadow-sm hover:border-[#b10303] hover:shadow-md transition-all group flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-[#fef2f2] rounded flex items-center justify-center text-xl group-hover:bg-red-50 group-hover:scale-110 transition-all">
                                        <i class="fa-solid fa-tv text-[#b10303]"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#1e293b]">Layar Display</div>
                                        <div class="text-[10px] text-[#64748b] font-bold uppercase tracking-widest">
                                            Buka di TV/Monitor</div>
                                    </div>
                                    <i
                                        class="fa-solid fa-arrow-up-right-from-square text-[#64748b] text-xs ml-auto"></i>
                                </a>
                                <a href="{{ route('visitor.register') }}" target="_blank"
                                    class="p-4 bg-white rounded-md border border-[#e2e8f0] shadow-sm hover:border-[#b10303] hover:shadow-md transition-all group flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-[#fef2f2] rounded flex items-center justify-center text-xl group-hover:bg-red-50 group-hover:scale-110 transition-all">
                                        <i class="fa-solid fa-mobile-screen-button text-[#b10303]"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#1e293b]">Mesin Antrian</div>
                                        <div class="text-[10px] text-[#64748b] font-bold uppercase tracking-widest">
                                            Untuk Visitor</div>
                                    </div>
                                    <i
                                        class="fa-solid fa-arrow-up-right-from-square text-[#64748b] text-xs ml-auto"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Active Operators -->
                        <div class="space-y-4">
                            <h2 class="text-lg font-bold text-[#1e293b] px-2">
                                <i class="fa-solid fa-users-viewfinder mr-2"></i> Status Operator
                            </h2>
                            <div class="bg-white rounded-md shadow-sm border border-[#e2e8f0] p-4 space-y-4">
                                @forelse($operators as $op)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded bg-[#fef2f2] flex items-center justify-center text-sm">
                                                <i class="fa-solid fa-user text-[#b10303]"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-[#1e293b]">{{ $op->name }}
                                                </div>
                                                <div
                                                    class="text-[9px] text-[#64748b] font-black uppercase tracking-widest">
                                                    {{ $op->loket_name }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <div
                                                class="w-2 h-2 rounded-full {{ $op->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}">
                                            </div>
                                            <span
                                                class="text-[9px] font-black uppercase tracking-tighter {{ $op->is_active ? 'text-emerald-600' : 'text-slate-500' }}">
                                                {{ $op->is_active ? 'Aktif' : 'Off' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-[#64748b] italic text-sm">
                                        <i class="fa-regular fa-circle-question block mb-1"></i>
                                        Belum ada operator
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
