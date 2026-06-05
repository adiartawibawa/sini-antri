<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operator – {{ Auth::user()->loket_name }}</title>
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
</head>

<body class="bg-[#fef2f2] text-[#1e293b] min-h-screen font-sans">

    <!-- Top Bar -->
    <div class="bg-[#b10303] text-white py-3 px-6 flex items-center justify-between">
        <div class="flex items-center gap-2 font-extrabold text-base">
            <i class="fa-solid fa-desktop"></i> Dashboard Operator
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

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-5 p-5">

        <!-- Left Column: Waiting Queue List -->
        <div class="order-2 lg:order-1">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-5 py-4 border-b border-[#e2e8f0] flex items-center justify-between">
                    <span class="font-bold text-[#1e293b]">
                        <i class="fa-regular fa-list-alt mr-2"></i> Daftar Antrian Menunggu
                    </span>
                    <span class="bg-[#b10303] text-white text-xs font-bold px-3 py-1 rounded-full"
                        id="waiting-badge">{{ $waitingQueues->count() }}</span>
                </div>
                <ul class="max-h-[520px] overflow-y-auto" id="queue-list">
                    @foreach ($waitingQueues as $q)
                        <li class="queue-item flex items-center gap-3 px-5 py-3 border-b border-[#e2e8f0] hover:bg-slate-50 transition"
                            id="queue-item-{{ $q->id }}" data-id="{{ $q->id }}">
                            <span
                                class="text-xl font-extrabold text-[#b10303] min-w-[60px]">{{ $q->queue_number }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-[#1e293b] truncate">{{ $q->visitor_name }}</div>
                                <div class="text-xs text-[#64748b] truncate">
                                    {{ $q->purpose ?: 'Tidak ada keterangan' }}</div>
                            </div>
                            <span
                                class="text-xs text-[#64748b] whitespace-nowrap">{{ $q->created_at->format('H:i') }}</span>
                        </li>
                    @endforeach
                    @if ($waitingQueues->isEmpty())
                        <li class="text-center py-10 text-[#64748b]" id="empty-state">
                            <i class="fa-regular fa-face-smile text-4xl block mb-2"></i>
                            Belum ada antrian yang menunggu
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Right Column: Active Queue & Stats -->
        <div class="order-1 lg:order-2 flex flex-col gap-5">

            <!-- Stats Cards -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="text-3xl font-extrabold text-[#b10303]" id="stat-waiting">{{ $waitingQueues->count() }}
                    </div>
                    <div class="text-xs text-[#64748b] mt-1"><i class="fa-regular fa-clock mr-1"></i> Menunggu</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="text-3xl font-extrabold text-[#059669]" id="stat-served">
                        {{ \App\Models\Antrian::where('status', 'completed')->whereDate('created_at', today())->count() }}
                    </div>
                    <div class="text-xs text-[#64748b] mt-1"><i class="fa-regular fa-circle-check mr-1"></i> Selesai
                        Hari Ini</div>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="text-3xl font-extrabold text-[#64748b]" id="stat-skipped">
                        {{ \App\Models\Antrian::where('status', 'skipped')->whereDate('created_at', today())->count() }}
                    </div>
                    <div class="text-xs text-[#64748b] mt-1"><i class="fa-solid fa-forward-step mr-1"></i> Dilewati
                    </div>
                </div>
            </div>

            <!-- Active Queue Panel -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-5 py-4 border-b border-[#e2e8f0] flex items-center justify-between">
                    <span class="font-bold text-[#1e293b]">
                        <i class="fa-regular fa-bell mr-2"></i> Sedang Dilayani
                    </span>
                    <span class="text-xs text-[#64748b]"><i class="fa-solid fa-building mr-1"></i>
                        {{ Auth::user()->loket_name }}</span>
                </div>

                <div class="p-6 text-center" id="active-box">
                    @if ($activeQueue)
                        <div class="text-xs text-[#64748b] uppercase tracking-wide">Nomor Antrian</div>
                        <div class="text-7xl font-black text-[#b10303] leading-none my-2" id="active-number">
                            {{ $activeQueue->queue_number }}</div>
                        <div class="font-semibold text-[#1e293b]" id="active-name">{{ $activeQueue->visitor_name }}
                        </div>
                        <div class="text-sm text-[#64748b] mt-1" id="active-purpose">{{ $activeQueue->purpose ?? '–' }}
                        </div>
                    @else
                        <div class="py-4 text-[#64748b]">
                            <i class="fa-regular fa-circle-check text-5xl block mb-3"></i>
                            <div class="font-semibold">Siap Melayani</div>
                            <div class="text-xs mt-1">Klik "Panggil" untuk memanggil antrian berikutnya</div>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-3 gap-3 p-5 pt-0">
                    <button
                        class="col-span-3 bg-[#b10303] hover:bg-[#8b0202] text-white font-bold py-3 rounded-lg transition active:scale-95 flex items-center justify-center gap-2 text-base"
                        id="btn-call" onclick="callQueue()">
                        <i class="fa-solid fa-bullhorn"></i> Panggil Berikutnya
                    </button>
                    <button
                        class="bg-amber-50 text-[#d97706] border-2 border-amber-200 font-bold py-2 rounded-lg transition active:scale-95 flex items-center justify-center gap-1 text-xs disabled:opacity-40 disabled:cursor-not-allowed"
                        id="btn-recall" @if (!$activeQueue) disabled @endif onclick="recallQueue()">
                        <i class="fa-solid fa-rotate-left"></i> Panggil Ulang
                    </button>
                    <button
                        class="bg-red-50 text-[#dc2626] border-2 border-red-200 font-bold py-2 rounded-lg transition active:scale-95 flex items-center justify-center gap-1 text-xs disabled:opacity-40 disabled:cursor-not-allowed"
                        id="btn-skip" @if (!$activeQueue) disabled @endif onclick="skipQueue()">
                        <i class="fa-solid fa-forward-step"></i> Lewati
                    </button>
                    <button
                        class="bg-green-50 text-[#059669] border-2 border-green-200 font-bold py-2 rounded-lg transition active:scale-95 flex items-center justify-center gap-1 text-xs disabled:opacity-40 disabled:cursor-not-allowed"
                        id="btn-done" @if (!$activeQueue) disabled @endif onclick="completeQueue()">
                        <i class="fa-regular fa-circle-check"></i> Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-6 right-6 bg-[#1e293b] text-white px-4 py-3 rounded-xl text-sm font-semibold max-w-sm shadow-lg z-50 hidden">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
            let activeQueueId = @json($activeQueue?->id);

            // Laravel Echo Listeners
            if (window.Echo) {
                window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                    updateConn(states.current === 'connected');
                });

                window.Echo.channel('operator-dashboard')
                    .listen('QueueCreated', (data) => {
                        addQueueItem(data);
                        updateStats(data.waiting_count);
                        showToast('🎟️ Antrian baru: ' + data.queue_number + ' – ' + data.visitor_name,
                            'success');
                    })
                    .listen('QueueStatusChanged', (data) => {
                        removeQueueItem(data.id);
                        updateStats(data.waiting_count);
                    })
                    .listen('QueueCalled', (data) => {
                        removeQueueItem(data.id);
                    });
            }

            function updateConn(ok) {
                const dot = document.getElementById('conn-dot');
                const label = document.getElementById('conn-label');
                if (dot) {
                    dot.className = 'w-2 h-2 rounded-full inline-block ' + (ok ? 'bg-green-500 animate-live-pulse' :
                        'bg-red-500');
                }
                if (label) label.textContent = ok ? 'Terhubung' : 'Terputus';
            }

            function addQueueItem(data) {
                const list = document.getElementById('queue-list');
                const empty = document.getElementById('empty-state');
                if (empty) empty.remove();

                if (document.getElementById('queue-item-' + data.id)) return;

                const li = document.createElement('li');
                li.className =
                    'flex items-center gap-3 px-5 py-3 border-b border-[#e2e8f0] hover:bg-slate-50 transition animate-slide-in';
                li.id = 'queue-item-' + data.id;
                li.dataset.id = data.id;
                li.innerHTML = `
            <span class="text-xl font-extrabold text-[#b10303] min-w-[60px]">${data.queue_number}</span>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-[#1e293b] truncate">${data.visitor_name}</div>
                <div class="text-xs text-[#64748b] truncate">${data.purpose || 'Tidak ada keterangan'}</div>
            </div>
            <span class="text-xs text-[#64748b] whitespace-nowrap">${data.created_at}</span>
        `;
                list.appendChild(li);
            }

            function removeQueueItem(id) {
                const el = document.getElementById('queue-item-' + id);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(20px)';
                    el.style.transition = 'opacity 0.3s, transform 0.3s';
                    setTimeout(() => {
                        el.remove();
                        if (document.querySelectorAll('.queue-item').length === 0) {
                            const list = document.getElementById('queue-list');
                            list.innerHTML = `<li class="text-center py-10 text-[#64748b]" id="empty-state">
                        <i class="fa-regular fa-face-smile text-4xl block mb-2"></i>
                        Belum ada antrian yang menunggu
                    </li>`;
                        }
                    }, 300);
                }
            }

            function updateStats(waitingCount) {
                document.getElementById('stat-waiting').textContent = waitingCount;
                document.getElementById('waiting-badge').textContent = waitingCount;
            }

            function updateActiveBox(queue) {
                const box = document.getElementById('active-box');
                if (queue) {
                    box.innerHTML = `
                <div class="text-xs text-[#64748b] uppercase tracking-wide">Nomor Antrian</div>
                <div class="text-7xl font-black text-[#b10303] leading-none my-2" id="active-number">${queue.queue_number}</div>
                <div class="font-semibold text-[#1e293b]" id="active-name">${queue.visitor_name}</div>
                <div class="text-sm text-[#64748b] mt-1" id="active-purpose">${queue.purpose || '–'}</div>
            `;
                    document.getElementById('btn-recall').disabled = false;
                    document.getElementById('btn-skip').disabled = false;
                    document.getElementById('btn-done').disabled = false;
                } else {
                    box.innerHTML = `
                <div class="py-4 text-[#64748b]">
                    <i class="fa-regular fa-circle-check text-5xl block mb-3"></i>
                    <div class="font-semibold">Siap Melayani</div>
                    <div class="text-xs mt-1">Klik "Panggil" untuk memanggil antrian berikutnya</div>
                </div>`;
                    document.getElementById('btn-recall').disabled = true;
                    document.getElementById('btn-skip').disabled = true;
                    document.getElementById('btn-done').disabled = true;
                }
            }

            window.callQueue = async function() {
                const btn = document.getElementById('btn-call');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

                try {
                    const res = await post('{{ route('operator.queue.call') }}', {});
                    const data = await res.json();

                    if (res.ok) {
                        activeQueueId = data.queue.id;
                        updateActiveBox(data.queue);
                        removeQueueItem(data.queue.id);
                        showToast('📢 Memanggil ' + data.queue_number, 'success');
                    } else {
                        showToast(data.message || 'Gagal memanggil antrian', 'error');
                    }
                } catch (e) {
                    showToast('Gagal terhubung ke server.', 'error');
                }

                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-bullhorn"></i> Panggil Berikutnya';
            }

            window.recallQueue = async function() {
                if (!activeQueueId) return;
                try {
                    const res = await post(`/operator/queue/${activeQueueId}/recall`, {});
                    const data = await res.json();
                    showToast(res.ok ? '🔁 ' + data.message : (data.message || 'Gagal'), res.ok ?
                        'success' : 'error');
                } catch (e) {
                    showToast('Gagal terhubung ke server.', 'error');
                }
            }

            window.skipQueue = async function() {
                if (!activeQueueId) return;
                if (!confirm('Lewati antrian ini? Pengunjung tidak akan dilayani.')) return;

                try {
                    const res = await post(`/operator/queue/${activeQueueId}/skip`, {});
                    const data = await res.json();

                    if (res.ok) {
                        activeQueueId = null;
                        updateActiveBox(null);
                        updateStats(data.waiting_count);
                        const el = document.getElementById('stat-skipped');
                        el.textContent = parseInt(el.textContent) + 1;
                        showToast('⏭️ Antrian dilewati.', 'success');
                    } else {
                        showToast(data.message || 'Gagal melewati antrian', 'error');
                    }
                } catch (e) {
                    showToast('Gagal terhubung ke server.', 'error');
                }
            }

            window.completeQueue = async function() {
                if (!activeQueueId) return;

                try {
                    const res = await post(`/operator/queue/${activeQueueId}/complete`, {});
                    const data = await res.json();

                    if (res.ok) {
                        activeQueueId = null;
                        updateActiveBox(null);
                        updateStats(data.waiting_count);
                        const el = document.getElementById('stat-served');
                        el.textContent = parseInt(el.textContent) + 1;
                        showToast('✅ Antrian selesai dilayani.', 'success');
                    } else {
                        showToast(data.message || 'Gagal menyelesaikan antrian', 'error');
                    }
                } catch (e) {
                    showToast('Gagal terhubung ke server.', 'error');
                }
            }

            function post(url, data) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(data),
                });
            }

            let toastTimer;

            function showToast(msg, type = 'success') {
                const el = document.getElementById('toast');
                el.textContent = msg;
                el.className =
                    'fixed bottom-6 right-6 px-4 py-3 rounded-xl text-sm font-semibold max-w-sm shadow-lg z-50 animate-toast-in ' +
                    (type === 'success' ? 'bg-[#059669]' : 'bg-[#dc2626]');
                el.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => {
                    el.classList.add('hidden');
                }, 3500);
            }
        });
    </script>
</body>

</html>
