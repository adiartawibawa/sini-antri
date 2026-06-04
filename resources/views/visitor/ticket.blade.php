<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tiket Antrian {{ $queue->queue_number }}</title>
@vite(['resources/css/app.css', 'resources/js/echo.js'])
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary: #1a56db;
    --bg: #f0f4ff;
    --card: #ffffff;
    --text: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --waiting:  #1a56db;
    --called:   #d97706;
    --serving:  #059669;
    --completed:#64748b;
    --skipped:  #dc2626;
  }
  body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--bg);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .card {
    background: var(--card);
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(26,86,219,.15);
    padding: 2.5rem 2rem;
    width: 100%; max-width: 400px;
    text-align: center;
  }

  /* === Nomor Antrian Besar === */
  .ticket-number {
    font-size: 5.5rem;
    font-weight: 900;
    letter-spacing: -.02em;
    line-height: 1;
    color: var(--primary);
    padding: 1.5rem 0;
    transition: transform .3s ease;
  }
  .ticket-number.pulse { animation: pulse 0.6s ease; }
  @keyframes pulse {
    0%,100% { transform: scale(1); }
    50%      { transform: scale(1.08); }
  }

  /* === Status Badge === */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .5rem 1.25rem;
    border-radius: 999px;
    font-size: .9rem;
    font-weight: 700;
    margin: .75rem 0 1.5rem;
    transition: all .4s ease;
  }
  .status-waiting   { background: #eff6ff; color: var(--waiting);   border: 2px solid #bfdbfe; }
  .status-called    { background: #fffbeb; color: var(--called);    border: 2px solid #fde68a; animation: blink 1s infinite; }
  .status-serving   { background: #f0fdf4; color: var(--serving);   border: 2px solid #6ee7b7; }
  .status-completed { background: #f8fafc; color: var(--completed); border: 2px solid #e2e8f0; }
  .status-skipped   { background: #fff1f2; color: var(--skipped);   border: 2px solid #fecaca; }
  @keyframes blink {
    0%,100% { opacity: 1; } 50% { opacity: .6; }
  }

  /* === Alert Box saat dipanggil === */
  .alert-called {
    display: none;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #78350f;
    border-radius: 16px;
    padding: 1.25rem;
    margin: 1rem 0;
    font-weight: 700;
    font-size: 1.05rem;
    animation: slideIn .4s ease;
  }
  .alert-called.show { display: block; }
  @keyframes slideIn {
    from { transform: translateY(-10px); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
  }

  /* === Info Cards === */
  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
    margin: 1.25rem 0;
  }
  .info-card {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: .875rem .5rem;
    transition: all .3s;
  }
  .info-number {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text);
  }
  .info-label {
    font-size: .72rem;
    color: var(--muted);
    margin-top: .2rem;
  }
  .info-number.updated { animation: flash .5s ease; }
  @keyframes flash {
    0%,100% { color: var(--text); }
    50%     { color: var(--primary); }
  }

  /* === Visitor Info === */
  .visitor-info {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1rem;
    text-align: left;
    margin-top: 1rem;
    font-size: .875rem;
  }
  .visitor-info .row {
    display: flex; justify-content: space-between;
    padding: .3rem 0;
    border-bottom: 1px solid var(--border);
  }
  .visitor-info .row:last-child { border-bottom: none; }
  .visitor-info .key { color: var(--muted); }
  .visitor-info .val { font-weight: 600; color: var(--text); }

  /* === Live indicator === */
  .live-dot {
    display: inline-block;
    width: 8px; height: 8px;
    background: #22c55e;
    border-radius: 50%;
    margin-right: .35rem;
    animation: livePulse 1.5s infinite;
  }
  @keyframes livePulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50%     { opacity: .5; transform: scale(.7); }
  }
  .live-label { font-size: .75rem; color: var(--muted); margin-top: 1rem; }

  .divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 1.25rem 0;
  }
</style>
</head>
<body>
<div class="card">

  <div style="font-size:.85rem; color:var(--muted); margin-bottom:.5rem;">
    🎟️ Nomor Antrian Anda
  </div>

  <div class="ticket-number" id="ticket-number">{{ $queue->queue_number }}</div>

  <div class="status-badge status-{{ $queue->status }}" id="status-badge">
    <span id="status-icon">
      @if($queue->status === 'waiting')   ⏳
      @elseif($queue->status === 'called')   📢
      @elseif($queue->status === 'serving')  ✅
      @elseif($queue->status === 'completed') 🏁
      @elseif($queue->status === 'skipped')  ⏭️
      @endif
    </span>
    <span id="status-text">
      @if($queue->status === 'waiting')   Menunggu Dipanggil
      @elseif($queue->status === 'called')   Silakan Menuju Loket!
      @elseif($queue->status === 'serving')  Sedang Dilayani
      @elseif($queue->status === 'completed') Selesai
      @elseif($queue->status === 'skipped')  Dilewati
      @endif
    </span>
  </div>

  <!-- Alert muncul saat dipanggil -->
  <div class="alert-called" id="alert-called">
    📢 Nomor <strong>{{ $queue->queue_number }}</strong> dipanggil!<br>
    Silakan menuju <span id="alert-loket">loket</span>.
  </div>

  <div class="info-grid">
    <div class="info-card">
      <div class="info-number" id="position-ahead">{{ $positionAhead }}</div>
      <div class="info-label">Antrian di depan</div>
    </div>
    <div class="info-card">
      <div class="info-number" id="est-minutes">
        @if($estimatedMinutes > 0) ~{{ $estimatedMinutes }} <span style="font-size:1rem">mnt</span>
        @else —
        @endif
      </div>
      <div class="info-label">Estimasi tunggu</div>
    </div>
  </div>

  <hr class="divider">

  <div class="visitor-info">
    <div class="row">
      <span class="key">Nama</span>
      <span class="val">{{ $queue->visitor_name }}</span>
    </div>
    @if($queue->purpose)
    <div class="row">
      <span class="key">Keperluan</span>
      <span class="val">{{ $queue->purpose }}</span>
    </div>
    @endif
    <div class="row">
      <span class="key">Waktu Ambil</span>
      <span class="val">{{ $queue->created_at->format('H:i') }} WIB</span>
    </div>
    <div class="row" id="loket-row" style="display:{{ $queue->operator ? 'flex' : 'none' }}">
      <span class="key">Loket</span>
      <span class="val" id="loket-name">{{ $queue->operator?->loket_name }}</span>
    </div>
  </div>

  <p class="live-label">
    <span class="live-dot"></span> Status terupdate otomatis secara real-time
  </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const UUID   = "{{ $queue->uuid }}";
    let STATUS = "{{ $queue->status }}";
    const AVG_PER_PERSON = {{ \App\Models\QueueSetting::first()?->avg_service_minutes ?? 5 }};

    // ---- Laravel Echo Listeners ----
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

    // ---- Update UI ----
    function updateStatus(status, loketName) {
        const badge     = document.getElementById('status-badge');
        const iconEl    = document.getElementById('status-icon');
        const textEl    = document.getElementById('status-text');
        const alertEl   = document.getElementById('alert-called');
        const ticketEl  = document.getElementById('ticket-number');

        badge.className = 'status-badge status-' + status;

        const labels = {
            waiting:   { icon: '⏳', text: 'Menunggu Dipanggil' },
            called:    { icon: '📢', text: 'Silakan Menuju Loket!' },
            serving:   { icon: '✅', text: 'Sedang Dilayani' },
            completed: { icon: '🏁', text: 'Selesai' },
            skipped:   { icon: '⏭️', text: 'Dilewati' },
        };

        if (labels[status]) {
            iconEl.textContent = labels[status].icon;
            textEl.textContent = labels[status].text;
        }

        if (status === 'called' && loketName) {
            alertEl.classList.add('show');
            document.getElementById('alert-loket').textContent = loketName;
            document.getElementById('loket-name').textContent  = loketName;
            document.getElementById('loket-row').style.display = 'flex';
        } else {
            alertEl.classList.remove('show');
        }

        ticketEl.classList.add('pulse');
        setTimeout(() => ticketEl.classList.remove('pulse'), 700);

        if (status === 'called' || status === 'serving' || status === 'completed' || status === 'skipped') {
            document.getElementById('position-ahead').textContent = '0';
            document.getElementById('est-minutes').textContent = '—';
        }
    }

    function refreshPosition() {
        if (STATUS !== 'waiting') return;
        
        fetch('/ticket/' + UUID + '/position')
            .then(r => r.json())
            .then(data => {
                const posEl  = document.getElementById('position-ahead');
                const estEl  = document.getElementById('est-minutes');
                posEl.textContent = data.position_ahead;
                posEl.classList.add('updated');
                setTimeout(() => posEl.classList.remove('updated'), 600);
                if (data.position_ahead > 0) {
                    estEl.innerHTML = '~' + (data.position_ahead * AVG_PER_PERSON) + ' <span style="font-size:1rem">mnt</span>';
                } else {
                    estEl.textContent = '—';
                }
            }).catch(() => {});
    }

    function playNotificationSound() {
        try {
            const ctx  = new (window.AudioContext || window.webkitAudioContext)();
            const osc  = ctx.createOscillator();
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
        } catch(e) {}
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
</body>
</html>
