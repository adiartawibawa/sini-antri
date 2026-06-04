<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian Digital</title>
    @vite(['resources/css/app.css', 'resources/js/echo.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans:wght@400;700;900&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #1a56db;
            --accent: #f59e0b;
            --bg: #0a0f1e;
            --card: #131929;
            --text: #f1f5f9;
            --muted: #64748b;
            --border: #1e293b;
        }

        body {
            font-family: 'Noto Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
            overflow: hidden;
        }

        header {
            background: var(--card);
            border-bottom: 2px solid var(--primary);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            color: white;
        }

        .brand span {
            color: var(--accent);
        }

        .clock {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--accent);
        }

        .main {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            background: var(--border);
            overflow: hidden;
        }

        .now-serving {
            background: var(--card);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
        }

        .ns-label {
            font-size: 1.1rem;
            color: var(--muted);
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .ns-number {
            font-family: 'Bebas Neue', sans-serif;
            font-size: min(22vw, 220px);
            color: white;
            text-shadow: 0 0 80px rgba(26, 86, 219, .5);
        }

        .ns-number.animate {
            animation: bigPulse .8s ease;
        }

        @keyframes bigPulse {
            0% {
                transform: scale(.8);
                opacity: 0;
            }

            60% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .ns-name {
            font-size: 1.8rem;
            font-weight: 900;
            margin-top: 1rem;
        }

        .ns-loket {
            margin-top: .75rem;
            background: var(--accent);
            color: #78350f;
            font-size: 1.2rem;
            font-weight: 800;
            padding: .5rem 2rem;
            border-radius: 999px;
        }

        .next-panel {
            background: var(--card);
            display: flex;
            flex-direction: column;
        }

        .next-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-size: .9rem;
            color: var(--muted);
            text-transform: uppercase;
        }

        .next-item {
            display: flex;
            align-items: center;
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .next-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.2rem;
            color: white;
            min-width: 90px;
        }

        .next-name {
            font-weight: 700;
        }

        footer {
            background: var(--primary);
            padding: .75rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            overflow: hidden;
        }

        .ticker-text {
            font-size: .9rem;
            animation: tickerScroll 20s linear infinite;
            white-space: nowrap;
        }

        @keyframes tickerScroll {
            0% {
                transform: translateX(100vw);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        #start-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 30, 0.95);
            z-index: 10000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .flash-overlay {
            position: fixed;
            inset: 0;
            background: rgba(26, 86, 219, .3);
            opacity: 0;
            pointer-events: none;
            z-index: 999;
        }

        .flash-overlay.active {
            animation: flashAnim .6s ease forwards;
        }

        @keyframes flashAnim {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="brand">Sistem <span>Antrian</span> Digital</div>
        <div class="clock" id="clock">00:00:00</div>
    </header>
    <div class="main">
        <div class="now-serving" id="now-serving">
            @if ($currentQueue)
                <div class="ns-label">🔊 Nomor Dipanggil</div>
                <div class="ns-number" id="ns-number">{{ $currentQueue->queue_number }}</div>
                <div class="ns-name" id="ns-name">{{ $currentQueue->visitor_name }}</div>
                <div class="ns-loket" id="ns-loket">{{ $currentQueue->operator?->loket_name ?? 'Loket' }}</div>
            @else
                <div style="opacity:.4;text-align:center;">
                    <div style="font-size:8rem">🟢</div>
                    <p style="font-size:1.2rem;font-weight:700">Menunggu Antrian...</p>
                </div>
            @endif
        </div>
        <div class="next-panel">
            <div class="next-header">📋 Antrian Selanjutnya</div>
            <div class="next-list" id="next-list">
                @foreach ($nextQueues as $i => $q)
                    <div class="next-item">
                        <div class="next-num">{{ $q->queue_number }}</div>
                        <div class="next-info">
                            <div class="next-name">{{ $q->visitor_name }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <footer>
        <div class="ticker-text">Selamat datang. Silakan ambil nomor antrian dan tunggu hingga nomor Anda dipanggil. 🙏
        </div>
    </footer>
    <div id="start-overlay">
        <div style="font-size:5rem">📢</div>
        <h2 style="color:white">Klik untuk Aktifkan Suara</h2>
    </div>
    <div class="flash-overlay" id="flash-overlay"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startOverlay = document.getElementById('start-overlay');
            startOverlay.addEventListener('click', () => {
                startOverlay.style.display = 'none';
                // WARM UP: Play a silent sound to unlock audio on all browsers
                const silent = new Audio(
                    'data:audio/wav;base64,UklGRigAAABXQVZFRm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA=='
                    );
                silent.play().catch(e => console.log('Warm up failed:', e));
                console.log('Audio system activated');
            });

            function updateClock() {
                document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID', {
                    hour12: false
                });
            }
            updateClock();
            setInterval(updateClock, 1000);

            async function playPlaylist(files) {
                console.log('Playing playlist:', files);
                for (const file of files) {
                    console.log('Current file:', file);
                    await new Promise(resolve => {
                        const audio = new Audio(file);
                        audio.onended = () => {
                            console.log('Finished:', file);
                            resolve();
                        };
                        audio.onerror = (e) => {
                            console.error('Error playing:', file, e);
                            resolve(); // Skip error and continue
                        };
                        audio.play().catch(err => {
                            console.error('Play failed:', file, err);
                            resolve();
                        });
                    });
                }
            }

            window.Echo.channel('display-screen').listen('QueueCalled', async (data) => {
                console.log('Event received:', data);
                updateDisplay(data.queue_number, data.visitor_name, data.loket_name);
                flashScreen();
                if (data.audio_playlist && data.audio_playlist.length > 0) {
                    await playPlaylist(data.audio_playlist);
                } else {
                    console.warn('No audio playlist received in event');
                }
                updateNextList();
            });

            function updateDisplay(number, name, loket) {
                const servingEl = document.getElementById('now-serving');
                servingEl.innerHTML =
                    `<div class="ns-label">🔊 Nomor Dipanggil</div><div class="ns-number animate" id="ns-number">${number}</div><div class="ns-name">${name}</div><div class="ns-loket">${loket}</div>`;
                setTimeout(() => document.getElementById('ns-number')?.classList.remove('animate'), 500);
            }

            function flashScreen() {
                const el = document.getElementById('flash-overlay');
                el.classList.remove('active');
                void el.offsetWidth;
                el.classList.add('active');
            }

            function updateNextList() {
                fetch('/display/status').then(r => r.json()).then(data => {
                    const list = document.getElementById('next-list');
                    list.innerHTML = data.next.map(q =>
                        `<div class="next-item"><div class="next-num">${q.queue_number}</div><div class="next-info"><div class="next-name">${q.visitor_name}</div></div></div>`
                        ).join('');
                });
            }
        });
    </script>
</body>

</html>
