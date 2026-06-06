@extends('layouts.display')

@section('title', 'Layar Antrian Digital')

@push('styles')
    <style>
        /* ─── Things Tailwind cannot express ─── */

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

        /* Red ambient glow */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(192, 57, 43, 0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Brand icon shine */
        .brand-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, transparent 60%);
        }

        /* Video right-edge fade */
        .video-fade::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, transparent 75%, #07090f 100%);
            pointer-events: none;
            z-index: 2;
        }

        /* Footer left/right fade edges */
        .ticker-fade::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 120px;
            background: linear-gradient(to right, #c0392b, transparent);
            z-index: 2;
            pointer-events: none;
        }

        .ticker-fade::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            width: 120px;
            background: linear-gradient(to left, #c0392b, transparent);
            z-index: 2;
            pointer-events: none;
        }

        /* Now-serving card top shimmer line */
        #now-serving-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #c0392b, transparent);
            opacity: 0;
            transition: opacity 0.5s;
        }

        #now-serving-card.active::before {
            opacity: 1;
        }

        /* Section label left bar */
        .section-label::before {
            content: '';
            display: block;
            width: 3px;
            height: 14px;
            background: #c0392b;
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* Queue waiting dot */
        .qi-status::before {
            content: '';
            display: block;
            width: 5px;
            height: 5px;
            background: rgba(250, 204, 21, 0.6);
            border-radius: 50%;
        }

        /* Thin scrollbar */
        #next-list::-webkit-scrollbar {
            width: 3px;
        }

        #next-list::-webkit-scrollbar-track {
            background: transparent;
        }

        #next-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 2px;
        }

        /* ─── Keyframes ─── */
        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            }

            50% {
                opacity: .7;
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0);
            }
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

        @keyframes ticker {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
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

        @keyframes nsEnter {
            0% {
                transform: scale(.75);
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

        /* ─── Animation utility classes ─── */
        .animate-blink {
            animation: blink 2s ease-in-out infinite;
        }

        .animate-ring-pulse {
            animation: ringPulse 2.5s ease-in-out infinite;
        }

        .animate-ticker {
            animation: ticker 35s linear infinite;
        }

        .animate-slide-in {
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-ns-enter {
            animation: nsEnter 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* ─── Custom font utility classes ─── */
        .font-display {
            font-family: 'Roboto Condensed', sans-serif;
        }

        .font-mono {
            font-family: 'Roboto Mono', monospace;
        }
    </style>
@endpush

@section('body_class',
    'font-[\'Roboto\',sans-serif] bg-[#07090f] text-[#f0f2f8] h-dvh overflow-hidden flex flex-col
    relative')

@section('content')
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

    {{-- ─── HEADER ─── --}}
    <header
        class="relative z-10 h-[72px] flex-shrink-0
                   flex items-center justify-between px-7
                   border-b border-white/[0.07]
                   bg-[rgba(7,9,15,0.7)] backdrop-blur-xl">

        {{-- Brand --}}
        <div class="flex items-center gap-3.5">
            <div
                class="brand-icon relative w-11 h-11 rounded-lg flex items-center justify-center
                        overflow-hidden flex-shrink-0 bg-[#c0392b]
                        shadow-[0_4px_20px_rgba(192,57,43,0.35)]">
                <img src="https://cdn-sdotid.adg.id/images/538b9b6d-01d4-48bd-88b4-ec673432266e_320x320.png"
                    class="w-full h-full object-cover" alt="Logo">
            </div>
            <div>
                <div class="font-display font-black text-[22px] leading-none uppercase tracking-wide">
                    SINI <span class="text-[#c0392b]">ANTRI</span> | SMKN 3 TABANAN
                </div>
                <div class="mt-1 text-[8px] font-semibold uppercase tracking-[0.18em] text-white/45">
                    Digital Queue System v1.0 - AdiArtaWibawa
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="flex items-center gap-6">
            {{-- Live badge --}}
            <div
                class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-full
                        bg-emerald-500/[0.08] border border-emerald-500/20
                        text-[11px] font-semibold uppercase tracking-[0.06em] text-emerald-400">
                <span class="animate-blink w-[7px] h-[7px] rounded-full bg-emerald-400 inline-block"></span>
                Sistem Aktif
            </div>
            {{-- Clock --}}
            <div class="text-right">
                <div id="clock" class="font-mono text-[28px] font-medium leading-none tracking-[0.04em] text-[#c0392b]">
                    00:00:00
                </div>
                <div class="mt-1 text-[10px] text-white/45 uppercase tracking-[0.12em]">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>
    </header>

    {{-- ─── MAIN ─── --}}
    <main class="flex-1 flex overflow-hidden relative z-[1] flex-col md:flex-row">

        {{-- Video Panel --}}
        <section class="video-fade relative bg-black overflow-hidden flex-[2.2]">
            <div id="youtube-player" class="absolute inset-0 w-full h-full"></div>
        </section>

        {{-- Queue Panel --}}
        <section
            class="flex-1 flex flex-col overflow-hidden
                        pt-5 px-5 pb-0
                        border-t border-white/[0.07]
                        md:border-t-0 md:border-l md:border-white/[0.07]
                        md:min-w-[320px] md:max-w-[420px]
                        max-sm:px-3.5
                        bg-[rgba(7,9,15,0.5)] backdrop-blur-[30px]">

            {{-- Now Serving --}}
            <div class="flex-shrink-0 mb-4">
                <div class="section-label flex items-center gap-2.5 mb-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/45">
                        Sedang Dilayani
                    </span>
                </div>

                <div id="now-serving-card"
                    class="relative overflow-hidden rounded-[14px] px-5 py-7 text-center
                            bg-white/[0.032] border border-white/[0.07]
                            transition-all duration-[400ms] ease-[cubic-bezier(0.16,1,0.3,1)]
                            {{ $currentQueue ? 'active !border-[rgba(192,57,43,0.4)] !bg-[rgba(192,57,43,0.05)]' : '' }}">

                    @if ($currentQueue)
                        <div class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/45 mb-2">
                            Nomor Antrian
                        </div>
                        <div id="ns-number"
                            class="animate-ns-enter font-display font-black text-[96px] max-sm:text-[72px]
                                    leading-[0.9] tracking-[-4px] text-[#f0f2f8]">
                            {{ $currentQueue->queue_number }}
                        </div>
                        <div id="ns-loket"
                            class="inline-flex items-center gap-1.5 mt-4 px-[18px] py-[7px] rounded-full
                                    bg-[rgba(192,57,43,0.18)] border border-[rgba(192,57,43,0.4)]
                                    text-[#e87870] text-xs font-bold tracking-[0.06em]">
                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                            {{ $currentQueue->operator?->loket_name ?? 'Loket' }}
                        </div>
                        <div id="ns-name"
                            class="mt-3.5 text-lg font-semibold text-[#f0f2f8]
                                    whitespace-nowrap overflow-hidden text-ellipsis px-3">
                            {{ $currentQueue->visitor_name }}
                        </div>
                    @else
                        <div class="py-8 opacity-30 flex flex-col items-center gap-3">
                            <div
                                class="w-14 h-14 rounded-full bg-white/[0.04] border border-white/[0.07]
                                        flex items-center justify-center text-[22px] text-white/45">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div class="text-[13px] font-medium text-white/45">
                                Menunggu antrian...
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Next Queues --}}
            <div class="flex-1 flex flex-col overflow-hidden min-h-0">
                <div class="section-label flex items-center gap-2.5 mb-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/45">
                        Antrian Berikutnya
                    </span>
                </div>

                <div id="next-list" class="flex-1 overflow-y-auto flex flex-col gap-2 pr-1 pb-4">
                    @forelse ($nextQueues as $i => $q)
                        <div class="animate-slide-in flex items-center gap-3.5 px-4 py-3.5
                                    rounded-lg bg-white/[0.04] border border-white/[0.07]
                                    hover:bg-white/[0.06] hover:border-white/[0.12]
                                    transition-[background,border-color] duration-200"
                            style="animation-delay: {{ $i * 80 }}ms">
                            <div
                                class="flex-shrink-0 w-[52px] h-[52px] rounded-lg
                                        flex items-center justify-center
                                        bg-[rgba(192,57,43,0.1)] border border-[rgba(192,57,43,0.2)]
                                        font-display text-[22px] font-bold text-[#c0392b]">
                                {{ $q->queue_number }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="text-[15px] font-semibold text-[#f0f2f8]
                                            overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $q->visitor_name }}
                                </div>
                                <div
                                    class="qi-status flex items-center gap-1.5 mt-0.5
                                            text-[10px] font-bold uppercase tracking-[0.1em] text-white/20">
                                    Menunggu
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center gap-2.5 opacity-25 p-8">
                            <i class="fa-regular fa-clock text-[28px] text-white/45"></i>
                            <p class="text-xs font-medium text-white/45 text-center italic">
                                Belum ada antrian lain
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    {{-- ─── TICKER FOOTER ─── --}}
    <footer
        class="ticker-fade relative z-10 h-[46px] flex-shrink-0 flex items-center
                   overflow-hidden bg-[#c0392b]">
        <div class="flex items-center whitespace-nowrap will-change-transform">
            <div id="ticker-content" class="animate-ticker flex items-center">
                <span class="text-sm font-bold text-white/[0.92] px-8">
                    Selamat Datang di Layanan Antrian Digital Sini Antri
                </span>
                <span class="w-[5px] h-[5px] rounded-full bg-white/50 flex-shrink-0"></span>
                <span class="text-sm font-bold text-white/[0.92] px-8">
                    Silakan ambil nomor antrian di mesin yang tersedia
                </span>
                <span class="w-[5px] h-[5px] rounded-full bg-white/50 flex-shrink-0"></span>
                <span class="text-sm font-bold text-white/[0.92] px-8">
                    Harap menunggu nomor antrian Anda dipanggil oleh petugas
                </span>
                <span class="w-[5px] h-[5px] rounded-full bg-white/50 flex-shrink-0"></span>
                <span class="text-sm font-bold text-white/[0.92] px-8">
                    Pastikan Anda membawa dokumen persyaratan yang diperlukan
                </span>
                <span class="w-[5px] h-[5px] rounded-full bg-white/50 flex-shrink-0"></span>
                <span class="text-sm font-bold text-white/[0.92] px-8">
                    Terima kasih atas kesabaran dan ketertiban Anda
                </span>
                <span class="w-[5px] h-[5px] rounded-full bg-white/50 flex-shrink-0"></span>
            </div>
        </div>
    </footer>

    {{-- Flash overlay --}}
    <div id="flash-overlay"
        class="fixed inset-0 bg-[rgba(192,57,43,0.15)] opacity-0 pointer-events-none
                z-[90] transition-opacity duration-300">
    </div>

    {{-- Start overlay --}}
    <div id="start-overlay"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center
                cursor-pointer bg-[rgba(7,9,15,0.96)] backdrop-blur-lg">
        <div
            class="animate-ring-pulse w-[120px] h-[120px] rounded-full
                    border-2 border-[rgba(192,57,43,0.3)]
                    flex items-center justify-center">
            <div
                class="w-[88px] h-[88px] rounded-full bg-[#c0392b]
                        flex items-center justify-center text-[34px] text-white
                        shadow-[0_8px_40px_rgba(192,57,43,0.5)]
                        transition-transform duration-200
                        [#start-overlay:hover_&]:scale-[1.06]">
                <i class="fa-solid fa-volume-high"></i>
            </div>
        </div>
        <div class="font-display font-black text-[26px] mt-7 tracking-[-0.5px]">
            Klik untuk Aktifkan Suara
        </div>
        <p class="mt-2 text-sm font-medium text-white/45">
            Layar akan otomatis memanggil nomor antrian
        </p>
    </div>
@endsection

@push('scripts')
    {{-- YouTube API --}}
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
                    onReady: () => console.log('YT Ready')
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('start-overlay');
            const audioQueue = [];
            let isProcessing = false;

            // ── Start overlay ──
            overlay.addEventListener('click', () => {
                overlay.style.transition = 'opacity 0.4s ease';
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 400);
                const silent = new Audio(
                    'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA=='
                );
                silent.play().catch(() => {});
                player?.unMute?.();
                player?.setVolume?.(75);
                player?.playVideo?.();
            });

            // ── Clock ──
            const clockEl = document.getElementById('clock');
            const tick = () => clockEl.textContent =
                new Date().toLocaleTimeString('id-ID', {
                    hour12: false
                });
            setInterval(tick, 1000);
            tick();

            // ── Audio helpers ──
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
                if (isProcessing || !audioQueue.length) return;
                isProcessing = true;
                player?.setVolume?.(10);
                await playPlaylist(audioQueue.shift());
                player?.setVolume?.(100);
                isProcessing = false;
                processAudioQueue();
            }

            // ── Realtime listener ──
            window.Echo.channel('display-screen').listen('QueueCalled', (data) => {
                updateDisplay(data.queue_number, data.visitor_name, data.loket_name);
                flashScreen();
                if (data.audio_playlist?.length) {
                    audioQueue.push(data.audio_playlist);
                    processAudioQueue();
                }
                refreshNextList();
            });

            function updateDisplay(number, name, loket) {
                const card = document.getElementById('now-serving-card');
                card.style.cssText += 'opacity:0;transform:scale(0.97);transition:all 0.25s ease';

                setTimeout(() => {
                    card.innerHTML = `
                        <div class="text-[10px] font-bold uppercase tracking-[0.22em] text-white/45 mb-2">
                            Nomor Antrian
                        </div>
                        <div id="ns-number"
                             class="animate-ns-enter font-display font-black text-[96px]
                                    leading-[0.9] tracking-[-4px] text-[#f0f2f8]">
                            ${number}
                        </div>
                        <div id="ns-loket"
                             class="inline-flex items-center gap-1.5 mt-4 px-[18px] py-[7px] rounded-full
                                    bg-[rgba(192,57,43,0.18)] border border-[rgba(192,57,43,0.4)]
                                    text-[#e87870] text-xs font-bold tracking-[0.06em]">
                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                            ${loket}
                        </div>
                        <div id="ns-name"
                             class="mt-3.5 text-lg font-semibold text-[#f0f2f8]
                                    whitespace-nowrap overflow-hidden text-ellipsis px-3">
                            ${name}
                        </div>
                    `;
                    card.classList.add('active');
                    card.style.cssText += 'opacity:1;transform:scale(1)';
                    setTimeout(() => card.classList.remove('active'), 3000);
                }, 250);
            }

            function flashScreen() {
                const el = document.getElementById('flash-overlay');
                el.style.opacity = '1';
                setTimeout(() => el.style.opacity = '0', 500);
            }

            function refreshNextList() {
                fetch('/display/status').then(r => r.json()).then(data => {
                    const list = document.getElementById('next-list');
                    if (!data.next?.length) {
                        list.innerHTML = `
                            <div class="flex-1 flex flex-col items-center justify-center gap-2.5 opacity-25 p-8">
                                <i class="fa-regular fa-clock text-[28px] text-white/45"></i>
                                <p class="text-xs font-medium text-white/45 text-center italic">
                                    Belum ada antrian lain
                                </p>
                            </div>`;
                        return;
                    }
                    list.innerHTML = data.next.map((q, i) => `
                        <div class="animate-slide-in flex items-center gap-3.5 px-4 py-3.5
                                    rounded-lg bg-white/[0.04] border border-white/[0.07]
                                    hover:bg-white/[0.06] hover:border-white/[0.12]
                                    transition-[background,border-color] duration-200"
                             style="animation-delay:${i * 80}ms">
                            <div class="flex-shrink-0 w-[52px] h-[52px] rounded-lg
                                        flex items-center justify-center
                                        bg-[rgba(192,57,43,0.1)] border border-[rgba(192,57,43,0.2)]
                                        font-display text-[22px] font-bold text-[#c0392b]">
                                ${q.queue_number}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[15px] font-semibold text-[#f0f2f8]
                                            overflow-hidden text-ellipsis whitespace-nowrap">
                                    ${q.visitor_name}
                                </div>
                                <div class="qi-status flex items-center gap-1.5 mt-0.5
                                            text-[10px] font-bold uppercase tracking-[0.1em] text-white/20">
                                    Menunggu
                                </div>
                            </div>
                        </div>
                    `).join('');
                });
            }

            // ── Seamless ticker loop ──
            const tc = document.getElementById('ticker-content');
            tc.innerHTML += tc.innerHTML;
        });
    </script>
@endpush
