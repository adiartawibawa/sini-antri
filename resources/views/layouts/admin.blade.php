<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sini Antri</title>
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
    @stack('styles')
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
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} flex items-center gap-3 px-4 py-3 rounded-md font-semibold transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-chart-line"></i></span> Dashboard
                </a>
                <a href="{{ route('admin.operators') }}"
                    class="sidebar-link {{ request()->routeIs('admin.operators') ? 'active' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} flex items-center gap-3 px-4 py-3 rounded-md font-semibold transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-users"></i></span> Operator
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} flex items-center gap-3 px-4 py-3 rounded-md font-semibold transition group">
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
                    <h1 class="text-xl font-bold text-[#1e293b]">@yield('header_title')</h1>
                    <p class="text-sm text-[#64748b] font-medium">@yield('header_subtitle')</p>
                </div>
                <div class="flex items-center gap-4">
                    @yield('header_actions')
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

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
