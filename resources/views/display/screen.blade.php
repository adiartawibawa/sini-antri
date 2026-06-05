<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian Digital - Sini Antri</title>
    @vite(['resources/css/app.css', 'resources/js/echo.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Roboto+Condensed:wght@700;900&family=Roboto+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --red: #c0392b;
            --red-dim: rgba(192, 57, 43, 0.18);
            --red-glow: rgba(192, 57, 43, 0.35);
            --bg-base: #07090f;
            --bg-card: rgba(255, 255, 255, 0.032);
            --bg-card-hover: rgba(255, 255, 255, 0.06);
            --border: rgba(255, 255, 255, 0.07);
            --border-accent: rgba(192, 57, 43, 0.4);
            --text-primary: #f0f2f8;
            --text-muted: rgba(200, 210, 230, 0.45);
            --text-dim: rgba(200, 210, 230, 0.2);
            --font-display: 'Roboto Condensed', sans-serif;
            --font-body: 'Roboto', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --radius: 14px;
            --radius-sm: 8px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg-base);
            color: var(--text-primary);
            height: 100dvh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Ambient background grid */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.018) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(192, 57, 43, 0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ─── HEADER ─── */
        header {
            position: relative;
            z-index: 10;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            border-bottom: 1px solid var(--border);
            background: rgba(7, 9, 15, 0.7);
            backdrop-filter: blur(20px);
            flex-shrink: 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: var(--red);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 20px var(--red-glow);
            position: relative;
            overflow: hidden;
        }

        .brand-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
        }

        .brand-name {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .brand-name span {
            color: var(--red);
        }

        .brand-tagline {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.18em;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 99px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            font-size: 11px;
            font-weight: 600;
            color: #10b981;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            }

            50% {
                opacity: 0.7;
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0);
            }
        }

        .clock-block {
            text-align: right;
        }

        #clock {
            font-family: var(--font-mono);
            font-size: 28px;
            font-weight: 500;
            color: var(--red);
            letter-spacing: 0.04em;
            line-height: 1;
        }

        .clock-date {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ─── MAIN ─── */
        main {
            flex: 1;
            display: flex;
            overflow: hidden;
            position: relative;
            z-index: 1;
            gap: 0;
        }

        /* ─── VIDEO PANEL ─── */
        .video-panel {
            flex: 2.2;
            position: relative;
            background: #000;
            overflow: hidden;
        }

        .video-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, transparent 75%, var(--bg-base) 100%);
            pointer-events: none;
            z-index: 2;
        }

        #youtube-player {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        /* ─── QUEUE PANEL ─── */
        .queue-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0;
            overflow: hidden;
            padding: 20px 20px 0;
            border-left: 1px solid var(--border);
            background: rgba(7, 9, 15, 0.5);
            backdrop-filter: blur(30px);
            min-width: 320px;
            max-width: 420px;
        }

        /* ─── SECTION LABEL ─── */
        .section-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .section-label span {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .section-label::before {
            content: '';
            display: block;
            width: 3px;
            height: 14px;
            background: var(--red);
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* ─── NOW SERVING CARD ─── */
        .serving-block {
            flex-shrink: 0;
            margin-bottom: 16px;
        }

        #now-serving-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #now-serving-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--red), transparent);
            opacity: 0;
            transition: opacity 0.5s;
        }

        #now-serving-card.active::before {
            opacity: 1;
        }

        #now-serving-card.active {
            border-color: var(--border-accent);
            background: rgba(192, 57, 43, 0.05);
        }

        .card-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .queue-number {
            font-family: var(--font-display);
            font-size: 96px;
            font-weight: 800;
            line-height: 0.9;
            letter-spacing: -4px;
            color: var(--text-primary);
        }

        .loket-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
            padding: 7px 18px;
            background: var(--red-dim);
            color: #e87870;
            border: 1px solid var(--border-accent);
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
        }

        .visitor-name {
            margin-top: 14px;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 12px;
        }

        .empty-state {
            padding: 32px 0;
            opacity: 0.3;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--text-muted);
        }

        .empty-text {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* ─── NEXT QUEUE LIST ─── */
        .next-block {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        #next-list {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-right: 4px;
            padding-bottom: 16px;
        }

        #next-list::-webkit-scrollbar {
            width: 3px;
        }

        #next-list::-webkit-scrollbar-track {
            background: transparent;
        }

        #next-list::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .queue-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            border: 1px solid var(--border);
            transition: background 0.2s, border-color 0.2s;
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .queue-item:hover {
            background: var(--bg-card-hover);
            border-color: rgba(255, 255, 255, 0.12);
        }

        @keyframes slideIn {
            from {
                transform: translateX(16px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .qi-num {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-sm);
            background: rgba(192, 57, 43, 0.1);
            border: 1px solid rgba(192, 57, 43, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: var(--red);
            flex-shrink: 0;
        }

        .qi-info {
            flex: 1;
            min-width: 0;
        }

        .qi-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .qi-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-dim);
            margin-top: 3px;
        }

        .qi-status::before {
            content: '';
            display: block;
            width: 5px;
            height: 5px;
            background: rgba(250, 204, 21, 0.6);
            border-radius: 50%;
        }

        .empty-list {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0.25;
            padding: 32px;
        }

        .empty-list i {
            font-size: 28px;
            color: var(--text-muted);
        }

        .empty-list p {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            text-align: center;
            font-style: italic;
        }

        /* ─── TICKER ─── */
        footer {
            position: relative;
            z-index: 10;
            height: 46px;
            background: var(--red);
            overflow: hidden;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        footer::before {
            content: '';
            position: absolute;
            inset-y: 0;
            left: 0;
            width: 120px;
            background: linear-gradient(to right, var(--red), transparent);
            z-index: 2;
            pointer-events: none;
        }

        footer::after {
            content: '';
            position: absolute;
            inset-y: 0;
            right: 0;
            width: 120px;
            background: linear-gradient(to left, var(--red), transparent);
            z-index: 2;
            pointer-events: none;
        }

        .ticker-track {
            display: flex;
            align-items: center;
            gap: 0;
            white-space: nowrap;
            will-change: transform;
        }

        .ticker-content {
            display: flex;
            align-items: center;
            gap: 0;
            animation: ticker 35s linear infinite;
        }

        .ticker-content span {
            font-size: 14px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.92);
            padding: 0 32px;
        }

        .ticker-dot {
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            flex-shrink: 0;
        }

        @keyframes ticker {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* ─── OVERLAYS ─── */
        #flash-overlay {
            position: fixed;
            inset: 0;
            background: rgba(192, 57, 43, 0.15);
            opacity: 0;
            pointer-events: none;
            z-index: 90;
            transition: opacity 0.3s ease;
        }

        #start-overlay {
            position: fixed;
            inset: 0;
            background: rgba(7, 9, 15, 0.96);
            z-index: 100;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(8px);
        }

        .overlay-icon-ring {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid rgba(192, 57, 43, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: ringPulse 2.5s ease-in-out infinite;
        }

        @keyframes ringPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(192, 57, 43, 0.4);
            }

            50% {
                box-shadow: 0 0 0 20px rgba(192, 57, 43, 0);
            }
        }

        .overlay-icon {
            width: 88px;
            height: 88px;
            background: var(--red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            color: #fff;
            box-shadow: 0 8px 40px rgba(192, 57, 43, 0.5);
            transition: transform 0.2s;
        }

        #start-overlay:hover .overlay-icon {
            transform: scale(1.06);
        }

        .overlay-title {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 800;
            margin-top: 28px;
            letter-spacing: -0.5px;
        }

        .overlay-sub {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 8px;
            font-weight: 500;
        }

        /* ─── ANIMATIONS ─── */
        .ns-enter {
            animation: nsEnter 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes nsEnter {
            0% {
                transform: scale(0.75);
                opacity: 0;
                filter: blur(8px);
            }

            60% {
                transform: scale(1.04);
                filter: blur(0);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            main {
                flex-direction: column;
            }

            .video-panel {
                flex: 1.2;
            }

            .video-panel::after {
                background: linear-gradient(to bottom, transparent 75%, var(--bg-base) 100%);
            }

            .queue-panel {
                flex: 1;
                min-width: unset;
                max-width: unset;
                border-left: none;
                border-top: 1px solid var(--border);
            }
        }

        @media (max-width: 600px) {
            header {
                padding: 0 16px;
                height: 60px;
            }

            .status-badge {
                display: none;
            }

            #clock {
                font-size: 22px;
            }

            .queue-panel {
                padding: 14px 14px 0;
            }

            .queue-number {
                font-size: 72px;
            }
        }
    </style>
</head>

<body>
    @php
        $youtubeId = 'dQw4w9WgXcQ';
        if ($setting?->youtube_url) {
            preg_match(
                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                $setting->youtube_url,
                $matches,
            );
            $youtubeId = $matches[1] ?? 'dQw4w9WgXcQ';
        }
    @endphp

    <!-- Header -->
    <header>
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <div class="brand-name">SINI <span>ANTRI</span></div>
                <div class="brand-tagline">Digital Queue System v2.0</div>
            </div>
        </div>

        <div class="header-right">
            <div class="status-badge">
                <span class="status-dot"></span>
                Sistem Aktif
            </div>
            <div class="clock-block">
                <div id="clock">00:00:00</div>
                <div class="clock-date">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main>
        <!-- Video Panel -->
        <section class="video-panel">
            <div id="youtube-player"></div>
        </section>

        <!-- Queue Panel -->
        <section class="queue-panel">
            <!-- Now Serving -->
            <div class="serving-block">
                <div class="section-label">
                    <span>Sedang Dilayani</span>
                </div>
                <div id="now-serving-card" class="{{ $currentQueue ? 'active' : '' }}">
                    @if ($currentQueue)
                        <div class="card-label">Nomor Antrian</div>
                        <div class="queue-number ns-enter" id="ns-number">{{ $currentQueue->queue_number }}</div>
                        <div class="loket-badge" id="ns-loket">
                            <i class="fa-solid fa-location-dot" style="font-size:10px;"></i>
                            {{ $currentQueue->operator?->loket_name ?? 'Loket' }}
                        </div>
                        <div class="visitor-name" id="ns-name">{{ $currentQueue->visitor_name }}</div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div class="empty-text">Menunggu antrian...</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Next Queues -->
            <div class="next-block">
                <div class="section-label">
                    <span>Antrian Berikutnya</span>
                </div>
                <div id="next-list">
                    @forelse ($nextQueues as $i => $q)
                        <div class="queue-item" style="animation-delay: {{ $i * 80 }}ms">
                            <div class="qi-num">{{ $q->queue_number }}</div>
                            <div class="qi-info">
                                <div class="qi-name">{{ $q->visitor_name }}</div>
                                <div class="qi-status">Menunggu</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-list">
                            <i class="fa-regular fa-clock"></i>
                            <p>Belum ada antrian lain</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Ticker -->
    <footer>
        <div class="ticker-track">
            <div class="ticker-content" id="ticker-content">
                <span>Selamat Datang di Layanan Antrian Digital Sini Antri</span>
                <span class="ticker-dot"></span>
                <span>Silakan ambil nomor antrian di mesin yang tersedia</span>
                <span class="ticker-dot"></span>
                <span>Harap menunggu nomor antrian Anda dipanggil oleh petugas</span>
                <span class="ticker-dot"></span>
                <span>Pastikan Anda membawa dokumen persyaratan yang diperlukan</span>
                <span class="ticker-dot"></span>
                <span>Terima kasih atas kesabaran dan ketertiban Anda</span>
                <span class="ticker-dot"></span>
            </div>
        </div>
    </footer>

    <!-- Flash Overlay -->
    <div id="flash-overlay"></div>

    <!-- Start Overlay -->
    <div id="start-overlay">
        <div class="overlay-icon-ring">
            <div class="overlay-icon">
                <i class="fa-solid fa-volume-high"></i>
            </div>
        </div>
        <div class="overlay-title">Klik untuk Aktifkan Suara</div>
        <p class="overlay-sub">Layar akan otomatis memanggil nomor antrian</p>
    </div>

    <!-- YouTube API -->
    <script src="https://www.youtube.com/iframe_api"></script>

    <script>
        let player;

        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-player', {
                height: '100%',
                width: '100%',
                videoId: '{{ $youtubeId }}',
                playerVars: {
                    autoplay: 1,
                    mute: 1,
                    controls: 0,
                    loop: 1,
                    playlist: '{{ $youtubeId }}',
                    modestbranding: 1,
                    rel: 0,
                    enablejsapi: 1
                },
                events: {
                    onReady: (e) => console.log('YT Ready')
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('start-overlay');
            const audioQueue = [];
            let isProcessing = false;

            overlay.addEventListener('click', () => {
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.4s ease';
                setTimeout(() => overlay.style.display = 'none', 400);
                const silent = new Audio(
                    'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA=='
                    );
                silent.play().catch(() => {});
                if (player?.unMute) {
                    player.unMute();
                    player.setVolume(100);
                    player.playVideo();
                }
            });

            function updateClock() {
                document.getElementById('clock').textContent =
                    new Date().toLocaleTimeString('id-ID', {
                        hour12: false
                    });
            }
            setInterval(updateClock, 1000);
            updateClock();

            async function playPlaylist(files) {
                for (const file of files) {
                    await new Promise(resolve => {
                        const a = new Audio(file);
                        a.onended = resolve;
                        a.onerror = resolve;
                        a.play().catch(resolve);
                    });
                }
            }

            async function processAudioQueue() {
                if (isProcessing || audioQueue.length === 0) return;
                isProcessing = true;
                if (player?.setVolume) player.setVolume(15);
                await playPlaylist(audioQueue.shift());
                if (player?.setVolume) player.setVolume(100);
                isProcessing = false;
                processAudioQueue();
            }

            window.Echo.channel('display-screen').listen('QueueCalled', (data) => {
                updateDisplay(data.queue_number, data.visitor_name, data.loket_name);
                flashScreen();
                if (data.audio_playlist?.length) {
                    audioQueue.push(data.audio_playlist);
                    processAudioQueue();
                }
                updateNextList();
            });

            function updateDisplay(number, name, loket) {
                const card = document.getElementById('now-serving-card');
                card.style.opacity = '0';
                card.style.transform = 'scale(0.97)';
                card.style.transition = 'all 0.25s ease';

                setTimeout(() => {
                    card.innerHTML = `
                        <div class="card-label">Nomor Antrian</div>
                        <div class="queue-number ns-enter" id="ns-number">${number}</div>
                        <div class="loket-badge" id="ns-loket">
                            <i class="fa-solid fa-location-dot" style="font-size:10px;"></i>
                            ${loket}
                        </div>
                        <div class="visitor-name" id="ns-name">${name}</div>
                    `;
                    card.classList.add('active');
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';

                    setTimeout(() => {
                        card.classList.remove('active');
                    }, 3000);
                }, 250);
            }

            function flashScreen() {
                const el = document.getElementById('flash-overlay');
                el.style.opacity = '1';
                setTimeout(() => el.style.opacity = '0', 500);
            }

            function updateNextList() {
                fetch('/display/status').then(r => r.json()).then(data => {
                    const list = document.getElementById('next-list');
                    if (!data.next?.length) {
                        list.innerHTML = `
                            <div class="empty-list">
                                <i class="fa-regular fa-clock"></i>
                                <p>Belum ada antrian lain</p>
                            </div>`;
                        return;
                    }
                    list.innerHTML = data.next.map((q, i) => `
                        <div class="queue-item" style="animation-delay:${i * 80}ms">
                            <div class="qi-num">${q.queue_number}</div>
                            <div class="qi-info">
                                <div class="qi-name">${q.visitor_name}</div>
                                <div class="qi-status">Menunggu</div>
                            </div>
                        </div>
                    `).join('');
                });
            }

            // Clone ticker for seamless loop
            const tc = document.getElementById('ticker-content');
            tc.innerHTML += tc.innerHTML;
        });
    </script>
</body>

</html>
