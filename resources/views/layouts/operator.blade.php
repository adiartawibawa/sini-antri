<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sini Antri</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/echo.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#b10303',
                        'primary-dark': '#8b0202',
                        bg: '#fef2f2',
                        card: '#ffffff',
                        text: '#1e293b',
                        muted: '#64748b',
                        border: '#e2e8f0',
                        success: '#059669',
                        warning: '#d97706',
                        danger: '#dc2626',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes slideIn {
            from {
                background: #fee2e2;
                transform: translateX(-8px);
            }

            to {
                background: transparent;
                transform: translateX(0);
            }
        }

        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        @keyframes toastIn {
            from {
                transform: translateY(100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.4s ease;
        }

        .animate-live-pulse {
            animation: livePulse 1.5s infinite;
        }

        .animate-toast-in {
            animation: toastIn 0.3s ease;
        }

        .queue-item-transition {
            transition: opacity 0.3s, transform 0.3s;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-[#fef2f2] text-[#1e293b] min-h-screen font-sans">

    @auth
        <!-- Top Bar -->
        <div class="bg-[#b10303] text-white py-3 px-6 flex items-center justify-between">
            <div class="flex items-center gap-2 font-extrabold text-base">
                <i class="fa-solid fa-desktop"></i> @yield('top_bar_title', 'Dashboard Operator')
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="flex items-center gap-1">
                    <span id="conn-dot" class="w-2 h-2 bg-green-500 rounded-full animate-live-pulse inline-block"></span>
                    <span id="conn-label" class="text-slate-400 text-xs">Terhubung</span>
                </span>
                <span class="bg-[#b10303] px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fa-solid fa-building mr-1"></i> {{ Auth::user()->loket_name }}
                </span>
                <span class="text-slate-400 text-xs">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="border border-white/30 text-white px-3 py-1 rounded-lg text-xs hover:bg-white/10 transition">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    @endauth

    @yield('content')

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-6 right-6 bg-[#1e293b] text-white px-4 py-3 rounded-xl text-sm font-semibold max-w-sm shadow-lg z-50 hidden">
    </div>

    @stack('scripts')
</body>

</html>
