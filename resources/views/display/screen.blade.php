<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian Digital - Sini Antri</title>
    @vite(['resources/css/app.css', 'resources/js/echo.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --font-bebas: 'Bebas Neue', sans-serif;
            --font-main: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-main);
        }

        .font-bebas {
            font-family: var(--font-bebas);
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .shadow-glow {
            box-shadow: 0 0 40px -10px rgba(177, 3, 3, 0.3);
        }

        @keyframes pulse-soft {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.9; transform: scale(0.98); }
        }

        .animate-pulse-soft {
            animation: pulse-soft 2s infinite ease-in-out;
        }

        @keyframes slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .animate-slide-up {
            animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>

<body class="bg-[#020617] text-slate-100 h-screen overflow-hidden flex flex-col">
    @php
        $youtubeId = 'dQw4w9WgXcQ';
        if ($setting?->youtube_url) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $setting->youtube_url, $matches);
            $youtubeId = $matches[1] ?? 'dQw4w9WgXcQ';
        }
    @endphp

    <!-- Header -->
    <header class="h-20 px-8 flex items-center justify-between border-b border-white/5 bg-slate-900/20">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[#b10303] rounded-md flex items-center justify-center text-2xl shadow-lg shadow-[#b10303]/20">
                <i class="fa-solid fa-ticket text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold tracking-tight">SINI <span class="text-[#b10303]">ANTRI</span></h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-bold">Digital Queue System v2.0</p>
            </div>
        </div>
        <div class="flex items-center gap-8">
            <div class="text-right">
                <div class="text-3xl font-bebas tracking-wider text-[#b10303] leading-none" id="clock">00:00:00</div>
                <div class="text-[10px] text-slate-500 font-bold uppercase mt-1">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden">
        <!-- Left: Video Panel -->
        <section class="flex-[2] relative bg-black overflow-hidden">
            <div id="youtube-player" class="absolute inset-0 w-full h-full"></div>
        </section>

        <!-- Right: Queue Panel -->
        <section class="flex-1 bg-slate-900/40 border-l border-white/5 p-6 flex flex-col gap-6">
            <!-- Now Serving -->
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between px-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Sedang Dilayani</span>
                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                </div>
                <div id="now-serving-card" class="glass rounded-md p-8 flex flex-col items-center justify-center text-center shadow-glow border-[#b10303]/20 transition-all duration-500">
                    @if ($currentQueue)
                        <div class="text-slate-400 text-sm font-semibold mb-2">NOMOR ANTRIAN</div>
                        <div class="font-bebas text-8xl text-white leading-none tracking-tighter" id="ns-number">
                            {{ $currentQueue->queue_number }}
                        </div>
                        <div class="mt-4 px-6 py-2 bg-[#b10303]/20 text-[#b10303] rounded-md text-sm font-bold border border-[#b10303]/30" id="ns-loket">
                            {{ $currentQueue->operator?->loket_name ?? 'Loket' }}
                        </div>
                        <div class="mt-4 text-xl font-bold text-slate-300 line-clamp-1" id="ns-name">
                            {{ $currentQueue->visitor_name }}
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center opacity-40">
                            <div class="text-6xl mb-4 text-slate-400">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <p class="font-bold text-slate-400 mt-4">Menunggu Antrian...</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Next Queues -->
            <div class="flex flex-col gap-3 min-h-0">
                <div class="px-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Antrian Berikutnya</span>
                </div>
                <div class="flex-1 glass rounded-md p-4 overflow-hidden flex flex-col gap-2" id="next-list">
                    @forelse ($nextQueues as $q)
                        <div class="flex items-center gap-4 p-4 rounded-md bg-white/5 border border-white/5 animate-slide-up">
                            <div class="w-16 h-16 rounded-md bg-slate-800 flex items-center justify-center font-bebas text-3xl text-[#b10303]">
                                {{ $q->queue_number }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-lg truncate">{{ $q->visitor_name }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Menunggu</div>
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-slate-600 font-medium italic">
                            Belum ada antrian lain
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Ticker -->
    <footer class="h-14 bg-[#b10303] flex items-center relative overflow-hidden">
        <div class="absolute inset-y-0 left-0 w-40 bg-gradient-to-r from-[#b10303] to-transparent z-10"></div>
        <div class="absolute inset-y-0 right-0 w-40 bg-gradient-to-l from-[#b10303] to-transparent z-10"></div>
        
        <div class="whitespace-nowrap flex items-center gap-12 text-white font-bold text-lg" id="ticker-container">
            <div class="ticker-content flex gap-12">
                <span>Selamat Datang di Layanan Antrian Digital Sini Antri</span>
                <span>•</span>
                <span>Silakan ambil nomor antrian di mesin yang tersedia</span>
                <span>•</span>
                <span>Harap menunggu nomor antrian Anda dipanggil oleh petugas</span>
                <span>•</span>
                <span>Pastikan Anda membawa dokumen persyaratan yang diperlukan</span>
                <span>•</span>
                <span>Terima kasih atas kesabaran Anda</span>
            </div>
        </div>
    </footer>

    <!-- Overlay & Flash -->
    <div id="start-overlay" class="fixed inset-0 bg-slate-950/95 z-[100] flex flex-col items-center justify-center cursor-pointer group">
        <div class="w-32 h-32 bg-[#b10303] rounded-full flex items-center justify-center text-5xl animate-bounce shadow-2xl shadow-[#b10303]/40 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-volume-high text-white"></i>
        </div>
        <h2 class="mt-8 text-2xl font-black tracking-tight">KLIK UNTUK AKTIFKAN SUARA</h2>
        <p class="mt-2 text-slate-500 font-medium">Layar akan otomatis memanggil nomor antrian</p>
    </div>
    
    <div id="flash-overlay" class="fixed inset-0 bg-[#b10303]/20 opacity-0 pointer-events-none z-[90] transition-opacity duration-300"></div>

    <!-- YouTube API Script -->
    <script src="https://www.youtube.com/iframe_api"></script>

    <style>
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .ticker-content {
            animation: ticker 30s linear infinite;
        }
        
        /* Pulse for current queue number */
        .ns-number-pulse {
            animation: bigPulse 1s ease-out;
        }

        @keyframes bigPulse {
            0% { transform: scale(0.8); opacity: 0; filter: blur(10px); }
            50% { transform: scale(1.1); filter: blur(0); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>

    <script>
        let player;
        
        // YouTube API Ready Callback
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-player', {
                height: '100%',
                width: '100%',
                videoId: '{{ $youtubeId }}',
                playerVars: {
                    'autoplay': 1,
                    'mute': 1,
                    'controls': 0,
                    'loop': 1,
                    'playlist': '{{ $youtubeId }}',
                    'modestbranding': 1,
                    'rel': 0,
                    'enablejsapi': 1
                },
                events: {
                    'onReady': (event) => {
                        console.log('YouTube Player Ready');
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const startOverlay = document.getElementById('start-overlay');
            const audioQueue = [];
            let isProcessingQueue = false;

            startOverlay.addEventListener('click', () => {
                startOverlay.style.display = 'none';
                // Warm up audio
                const silent = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFRm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA==');
                silent.play().catch(() => {});
                
                // Unmute YouTube player
                if (player && typeof player.unMute === 'function') {
                    player.unMute();
                    player.setVolume(100);
                    player.playVideo();
                }
            });

            function updateClock() {
                document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID', {
                    hour12: false
                });
            }
            setInterval(updateClock, 1000);
            updateClock();

            async function playPlaylist(files) {
                for (const file of files) {
                    await new Promise(resolve => {
                        const audio = new Audio(file);
                        audio.onended = () => resolve();
                        audio.onerror = () => resolve();
                        audio.play().catch(() => resolve());
                    });
                }
            }

            async function processAudioQueue() {
                if (isProcessingQueue || audioQueue.length === 0) return;
                isProcessingQueue = true;

                // DUCK VOLUME: Turunkan suara YouTube saat pengumuman
                if (player && typeof player.setVolume === 'function') {
                    player.setVolume(10);
                }

                const playlist = audioQueue.shift();
                await playPlaylist(playlist);
                
                // RESTORE VOLUME: Kembalikan suara YouTube setelah selesai
                if (player && typeof player.setVolume === 'function') {
                    player.setVolume(100);
                }

                isProcessingQueue = false;
                processAudioQueue();
            }

            window.Echo.channel('display-screen').listen('QueueCalled', (data) => {
                updateDisplay(data.queue_number, data.visitor_name, data.loket_name);
                flashScreen();
                
                if (data.audio_playlist && data.audio_playlist.length > 0) {
                    audioQueue.push(data.audio_playlist);
                    processAudioQueue();
                }
                
                updateNextList();
            });

            function updateDisplay(number, name, loket) {
                const card = document.getElementById('now-serving-card');
                
                // Update content with animation
                card.classList.add('scale-95', 'opacity-50');
                
                setTimeout(() => {
                    card.innerHTML = `
                        <div class="text-slate-400 text-sm font-semibold mb-2 animate-slide-up">NOMOR ANTRIAN</div>
                        <div class="font-bebas text-8xl text-white leading-none tracking-tighter ns-number-pulse" id="ns-number">${number}</div>
                        <div class="mt-4 px-6 py-2 bg-[#b10303]/20 text-[#b10303] rounded-md text-sm font-bold border border-[#b10303]/30 animate-slide-up" id="ns-loket">${loket}</div>
                        <div class="mt-4 text-xl font-bold text-slate-300 line-clamp-1 animate-slide-up" id="ns-name">${name}</div>
                    `;
                    card.classList.remove('scale-95', 'opacity-50');
                    card.classList.add('border-[#b10303]/50', 'shadow-[#b10303]/20');
                    
                    setTimeout(() => {
                        card.classList.remove('border-[#b10303]/50', 'shadow-[#b10303]/20');
                    }, 2000);
                }, 300);
            }

            function flashScreen() {
                const el = document.getElementById('flash-overlay');
                el.classList.add('opacity-100');
                setTimeout(() => el.classList.remove('opacity-100'), 500);
            }

            function updateNextList() {
                fetch('/display/status').then(r => r.json()).then(data => {
                    const list = document.getElementById('next-list');
                    if (data.next.length === 0) {
                        list.innerHTML = '<div class="flex-1 flex items-center justify-center text-slate-600 font-medium italic">Belum ada antrian lain</div>';
                        return;
                    }
                    
                    list.innerHTML = data.next.map((q, i) => `
                        <div class="flex items-center gap-4 p-4 rounded-md bg-white/5 border border-white/5 animate-slide-up" style="animation-delay: ${i * 100}ms">
                            <div class="w-16 h-16 rounded-md bg-slate-800 flex items-center justify-center font-bebas text-3xl text-[#b10303]">
                                ${q.queue_number}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-lg truncate">${q.visitor_name}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Menunggu</div>
                            </div>
                        </div>
                    `).join('');
                });
            }

            // Clone ticker content for infinite loop
            const tickerContent = document.querySelector('.ticker-content');
            tickerContent.innerHTML += tickerContent.innerHTML;
        });
    </script>
</body>

</html>
