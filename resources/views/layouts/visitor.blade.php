<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sini Antri</title>
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
                        waiting: '#1a56db',
                        called: '#d97706',
                        serving: '#059669',
                        completed: '#64748b',
                        skipped: '#dc2626',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
            }
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes flash {

            0%,
            100% {
                color: #1e293b;
            }

            50% {
                color: #b10303;
            }
        }

        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.7);
            }
        }

        .animate-pulse-scale {
            animation: pulse 0.6s ease;
        }

        .animate-blink {
            animation: blink 1s infinite;
        }

        .animate-slide-in {
            animation: slideIn 0.4s ease;
        }

        .animate-flash {
            animation: flash 0.5s ease;
        }

        .animate-live-pulse {
            animation: livePulse 1.5s infinite;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-[#fef2f2] min-h-screen flex items-center justify-center p-4 font-sans">

    @yield('content')

    @stack('scripts')
</body>

</html>
