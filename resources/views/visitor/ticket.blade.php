@extends('layouts.visitor')

@section('title', 'Tiket Antrian ' . $queue->queue_number)

@section('content')
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md text-center">

        <!-- Header -->
        <div class="text-xs text-[#64748b] mb-2">
            <i class="fa-solid fa-ticket mr-1"></i> Nomor Antrian Anda
        </div>

        <!-- Ticket Number -->
        <div class="text-7xl font-black tracking-tighter leading-none text-[#b10303] py-6 transition-transform"
            id="ticket-number">{{ $queue->queue_number }}</div>

        <!-- Status Badge -->
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 mb-4
            {{ $queue->status === 'waiting' ? 'bg-blue-50 text-[#1a56db] border-2 border-blue-200' : '' }}
            {{ $queue->status === 'called' ? 'bg-amber-50 text-[#d97706] border-2 border-amber-200 animate-blink' : '' }}
            {{ $queue->status === 'serving' ? 'bg-green-50 text-[#059669] border-2 border-green-200' : '' }}
            {{ $queue->status === 'completed' ? 'bg-slate-50 text-[#64748b] border-2 border-slate-200' : '' }}
            {{ $queue->status === 'skipped' ? 'bg-red-50 text-[#dc2626] border-2 border-red-200' : '' }}"
            id="status-badge">
            <span id="status-icon">
                @if ($queue->status === 'waiting')
                    <i class="fa-regular fa-clock"></i>
                @elseif($queue->status === 'called')
                    <i class="fa-solid fa-bullhorn"></i>
                @elseif($queue->status === 'serving')
                    <i class="fa-regular fa-circle-check"></i>
                @elseif($queue->status === 'completed')
                    <i class="fa-solid fa-flag-checkered"></i>
                @elseif($queue->status === 'skipped')
                    <i class="fa-solid fa-forward-step"></i>
                @endif
            </span>
            <span id="status-text">
                @if ($queue->status === 'waiting')
                    Menunggu Dipanggil
                @elseif($queue->status === 'called')
                    Silakan Menuju Loket!
                @elseif($queue->status === 'serving')
                    Sedang Dilayani
                @elseif($queue->status === 'completed')
                    Selesai
                @elseif($queue->status === 'skipped')
                    Dilewati
                @endif
            </span>
        </div>

        <!-- Alert Called -->
        <div class="hidden bg-gradient-to-r from-amber-400 to-orange-500 text-amber-900 rounded-xl p-4 my-4 font-bold animate-slide-in"
            id="alert-called">
            <i class="fa-solid fa-bullhorn mr-2"></i> Nomor <strong>{{ $queue->queue_number }}</strong> dipanggil!<br>
            Silakan menuju <span id="alert-loket">loket</span>.
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-3 my-5">
            <div class="bg-slate-50 border border-[#e2e8f0] rounded-xl py-3 px-2 transition-all">
                <div class="text-2xl font-extrabold text-[#1e293b]" id="position-ahead">{{ $positionAhead }}</div>
                <div class="text-xs text-[#64748b]">Antrian di depan</div>
            </div>
            <div class="bg-slate-50 border border-[#e2e8f0] rounded-xl py-3 px-2 transition-all">
                <div class="text-2xl font-extrabold text-[#1e293b]" id="est-minutes">
                    @if ($estimatedMinutes > 0)
                        ~{{ $estimatedMinutes }} <span class="text-sm">mnt</span>
                    @else
                        —
                    @endif
                </div>
                <div class="text-xs text-[#64748b]">Estimasi tunggu</div>
            </div>
        </div>

        <hr class="border-t border-[#e2e8f0] my-5">

        <!-- Visitor Info -->
        <div class="bg-slate-50 rounded-xl p-4 text-left text-sm">
            <div class="flex justify-between py-1 border-b border-[#e2e8f0]">
                <span class="text-[#64748b]"><i class="fa-regular fa-user mr-1"></i> Nama</span>
                <span class="font-semibold text-[#1e293b]">{{ $queue->visitor_name }}</span>
            </div>
            @if ($queue->purpose)
                <div class="flex justify-between py-1 border-b border-[#e2e8f0]">
                    <span class="text-[#64748b]"><i class="fa-regular fa-clipboard mr-1"></i> Keperluan</span>
                    <span class="font-semibold text-[#1e293b]">{{ $queue->purpose }}</span>
                </div>
            @endif
            <div class="flex justify-between py-1 border-b border-[#e2e8f0]">
                <span class="text-[#64748b]"><i class="fa-regular fa-clock mr-1"></i> Waktu Ambil</span>
                <span class="font-semibold text-[#1e293b]">{{ $queue->created_at->format('H:i') }} WITA</span>
            </div>
            <div class="flex justify-between py-1" id="loket-row"
                style="display:{{ $queue->operator ? 'flex' : 'none' }}">
                <span class="text-[#64748b]"><i class="fa-solid fa-building mr-1"></i> Loket</span>
                <span class="font-semibold text-[#1e293b]" id="loket-name">{{ $queue->operator?->loket_name }}</span>
            </div>
        </div>

        <!-- Live Indicator -->
        <p class="text-xs text-[#64748b] mt-4">
            <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-live-pulse mr-1"></span>
            Status terupdate otomatis secara real-time
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const UUID = "{{ $queue->uuid }}";
            let STATUS = "{{ $queue->status }}";
            const AVG_PER_PERSON = {{ \App\Models\QueueSetting::first()?->avg_service_minutes ?? 5 }};

            // Laravel Echo Listeners
            window.Echo.channel('ticket.' + UUID)
                .listen('QueueCalled', (data) => {
                    updateStatus('called', data.loket_name);
                    playNotificationSound();
                    vibrate();
                    STATUS = 'called';
                })
                .listen('QueueStatusChanged', (data) => {
                    updateStatus(data.status);
                    refreshPosition();
                    STATUS = data.status;
                });

            window.Echo.channel('operator-dashboard')
                .listen('QueueCalled', (data) => {
                    if (STATUS === 'waiting') {
                        refreshPosition();
                    }
                });

            function updateStatus(status, loketName) {
                const badge = document.getElementById('status-badge');
                const iconEl = document.getElementById('status-icon');
                const textEl = document.getElementById('status-text');
                const alertEl = document.getElementById('alert-called');
                const ticketEl = document.getElementById('ticket-number');

                // Update badge classes
                badge.className =
                    'inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 ';
                if (status === 'waiting') badge.classList.add('bg-blue-50', 'text-[#1a56db]', 'border-2',
                    'border-blue-200');
                else if (status === 'called') badge.classList.add('bg-amber-50', 'text-[#d97706]', 'border-2',
                    'border-amber-200', 'animate-blink');
                else if (status === 'serving') badge.classList.add('bg-green-50', 'text-[#059669]', 'border-2',
                    'border-green-200');
                else if (status === 'completed') badge.classList.add('bg-slate-50', 'text-[#64748b]', 'border-2',
                    'border-slate-200');
                else if (status === 'skipped') badge.classList.add('bg-red-50', 'text-[#dc2626]', 'border-2',
                    'border-red-200');

                const labels = {
                    waiting: {
                        icon: '<i class="fa-regular fa-clock"></i>',
                        text: 'Menunggu Dipanggil'
                    },
                    called: {
                        icon: '<i class="fa-solid fa-bullhorn"></i>',
                        text: 'Silakan Menuju Loket!'
                    },
                    serving: {
                        icon: '<i class="fa-regular fa-circle-check"></i>',
                        text: 'Sedang Dilayani'
                    },
                    completed: {
                        icon: '<i class="fa-solid fa-flag-checkered"></i>',
                        text: 'Selesai'
                    },
                    skipped: {
                        icon: '<i class="fa-solid fa-forward-step"></i>',
                        text: 'Dilewati'
                    },
                };

                if (labels[status]) {
                    iconEl.innerHTML = labels[status].icon;
                    textEl.textContent = labels[status].text;
                }

                if (status === 'called' && loketName) {
                    alertEl.classList.remove('hidden');
                    document.getElementById('alert-loket').textContent = loketName;
                    document.getElementById('loket-name').textContent = loketName;
                    document.getElementById('loket-row').style.display = 'flex';
                } else {
                    alertEl.classList.add('hidden');
                }

                ticketEl.classList.add('animate-pulse-scale');
                setTimeout(() => ticketEl.classList.remove('animate-pulse-scale'), 700);

                if (['called', 'serving', 'completed', 'skipped'].includes(status)) {
                    document.getElementById('position-ahead').textContent = '0';
                    document.getElementById('est-minutes').textContent = '—';
                }
            }

            function refreshPosition() {
                if (STATUS !== 'waiting') return;

                fetch('/ticket/' + UUID + '/position')
                    .then(r => r.json())
                    .then(data => {
                        const posEl = document.getElementById('position-ahead');
                        const estEl = document.getElementById('est-minutes');
                        posEl.textContent = data.position_ahead;
                        posEl.classList.add('animate-flash');
                        setTimeout(() => posEl.classList.remove('animate-flash'), 600);
                        if (data.position_ahead > 0) {
                            estEl.innerHTML = '~' + (data.position_ahead * AVG_PER_PERSON) +
                                ' <span class="text-sm">mnt</span>';
                        } else {
                            estEl.textContent = '—';
                        }
                    }).catch(() => {});
            }

            function playNotificationSound() {
                try {
                    const ctx = new(window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.setValueAtTime(660, ctx.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.4, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.6);
                } catch (e) {}
            }

            function vibrate() {
                if ('vibrate' in navigator) navigator.vibrate([200, 100, 200]);
            }

            // Polling fallback
            setInterval(() => {
                if (window.Echo.connector.pusher.connection.state !== 'connected') {
                    refreshPosition();
                }
            }, 30000);
        });
    </script>
@endpush
