<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Layar Antrian Digital</title>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans:wght@400;700;900&display=swap');

  * { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary: #1a56db;
    --accent:  #f59e0b;
    --bg:      #0a0f1e;
    --card:    #131929;
    --text:    #f1f5f9;
    --muted:   #64748b;
    --border:  #1e293b;
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

  /* ---- Header ---- */
  header {
    background: var(--card);
    border-bottom: 2px solid var(--primary);
    padding: 1rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
  }
  .brand {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem; letter-spacing: .05em;
    color: white;
  }
  .brand span { color: var(--accent); }
  .clock {
    font-size: 2.5rem; font-weight: 900;
    font-variant-numeric: tabular-nums;
    color: var(--accent);
    letter-spacing: .05em;
  }
  .date-label { font-size: .8rem; color: var(--muted); text-align: right; }

  /* ---- Main Area ---- */
  .main {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 2px;
    background: var(--border);
    overflow: hidden;
  }

  /* ---- Now Serving (Kiri) ---- */
  .now-serving {
    background: var(--card);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
  }
  .now-serving::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at center, rgba(26,86,219,.15) 0%, transparent 70%);
    pointer-events: none;
  }
  .ns-label {
    font-size: 1.1rem; font-weight: 700;
    letter-spacing: .15em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 1rem;
  }
  .ns-number {
    font-family: 'Bebas Neue', sans-serif;
    font-size: min(22vw, 220px);
    line-height: .9;
    color: white;
    text-shadow: 0 0 80px rgba(26,86,219,.5);
    transition: all .5s ease;
    position: relative; z-index: 1;
  }
  .ns-number.animate { animation: bigPulse .8s ease; }
  @keyframes bigPulse {
    0%   { transform: scale(.8); opacity: 0; }
    60%  { transform: scale(1.05); }
    100% { transform: scale(1); opacity: 1; }
  }

  .ns-name {
    font-size: 1.8rem; font-weight: 900;
    margin-top: 1rem; color: var(--text);
    text-align: center;
  }
  .ns-loket {
    margin-top: .75rem;
    background: var(--accent);
    color: #78350f;
    font-size: 1.2rem; font-weight: 800;
    padding: .5rem 2rem; border-radius: 999px;
    letter-spacing: .05em;
  }

  /* Flash overlay saat dipanggil */
  .flash-overlay {
    position: fixed; inset: 0;
    background: rgba(26,86,219,.3);
    opacity: 0; pointer-events: none;
    transition: opacity .3s;
    z-index: 999;
  }
  .flash-overlay.active { animation: flashAnim .6s ease forwards; }
  @keyframes flashAnim {
    0%   { opacity: 1; }
    100% { opacity: 0; }
  }

  /* ---- Next Queue (Kanan) ---- */
  .next-panel {
    background: var(--card);
    display: flex; flex-direction: column;
  }
  .next-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    font-size: .9rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted);
  }
  .next-list { flex: 1; overflow: hidden; }
  .next-item {
    display: flex; align-items: center;
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    transition: all .3s;
  }
  .next-item:first-child {
    background: rgba(26,86,219,.15);
    border-left: 4px solid var(--primary);
  }
  .next-item:first-child .next-num { color: var(--accent); }
  .next-num {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.2rem; color: white;
    min-width: 90px; letter-spacing: .03em;
  }
  .next-info { flex: 1; }
  .next-name { font-weight: 700; font-size: 1rem; }
  .next-pos {
    font-size: .78rem; color: var(--muted);
    margin-top: .15rem;
  }

  /* ---- Ticker Bar (Bottom) ---- */
  footer {
    background: var(--primary);
    padding: .75rem 2rem;
    display: flex; align-items: center; gap: 1rem;
    overflow: hidden;
  }
  .ticker-label {
    font-size: .8rem; font-weight: 800;
    letter-spacing: .1em; text-transform: uppercase;
    white-space: nowrap; padding: .3rem .75rem;
    background: rgba(0,0,0,.3); border-radius: 6px;
    flex-shrink: 0;
  }
  .ticker-text {
    font-size: .9rem; font-weight: 600;
    animation: tickerScroll 20s linear infinite;
    white-space: nowrap;
  }
  @keyframes tickerScroll {
    0%   { transform: translateX(100vw); }
    100% { transform: translateX(-100%); }
  }

  /* ---- No queue state ---- */
  .no-queue {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 3rem;
    opacity: .4;
  }
  .no-queue .big { font-size: 8rem; line-height: 1; }
  .no-queue p { font-size: 1.2rem; font-weight: 700; margin-top: 1rem; }
</style>
</head>
<body>

<!-- Header -->
<header>
  <div class="brand">Sistem <span>Antrian</span> Digital</div>
  <div>
    <div class="clock" id="clock">00:00:00</div>
    <div class="date-label" id="date-label"></div>
  </div>
</header>

<!-- Main -->
<div class="main">

  <!-- Kiri: Sedang Dipanggil -->
  <div class="now-serving" id="now-serving">
    @if($currentQueue)
      <div class="ns-label">🔊 Nomor Dipanggil</div>
      <div class="ns-number" id="ns-number">{{ $currentQueue->queue_number }}</div>
      <div class="ns-name" id="ns-name">{{ $currentQueue->visitor_name }}</div>
      <div class="ns-loket" id="ns-loket">{{ $currentQueue->operator?->loket_name ?? 'Loket' }}</div>
    @else
      <div class="no-queue">
        <div class="big">🟢</div>
        <p>Menunggu Antrian...</p>
      </div>
    @endif
  </div>

  <!-- Kanan: Antrian Berikutnya -->
  <div class="next-panel">
    <div class="next-header">📋 Antrian Selanjutnya</div>
    <div class="next-list" id="next-list">
      @forelse($nextQueues as $i => $q)
        <div class="next-item" id="next-{{ $q->id }}">
          <div class="next-num">{{ $q->queue_number }}</div>
          <div class="next-info">
            <div class="next-name">{{ $q->visitor_name }}</div>
            <div class="next-pos">Urutan ke-{{ $i + 1 }}</div>
          </div>
        </div>
      @empty
        <div style="padding:2rem;text-align:center;color:var(--muted)">
          Tidak ada antrian berikutnya
        </div>
      @endforelse
    </div>
  </div>

</div>

<!-- Ticker Footer -->
<footer>
  <div class="ticker-label">📢 INFO</div>
  <div class="ticker-text" id="ticker">
    Selamat datang. Silakan ambil nomor antrian dan tunggu hingga nomor Anda dipanggil.
    Harap tetap di area tunggu dan perhatikan layar ini. Terima kasih atas kesabaran Anda. 🙏
  </div>
</footer>

<!-- Flash Overlay -->
<div class="flash-overlay" id="flash-overlay"></div>

<script>
const PUSHER_KEY     = "{{ config('broadcasting.connections.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

// ---- Clock ----
function updateClock() {
  const now = new Date();
  document.getElementById('clock').textContent =
    now.toLocaleTimeString('id-ID', { hour12: false });
  document.getElementById('date-label').textContent =
    now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}
updateClock();
setInterval(updateClock, 1000);

// ---- Pusher ----
const pusher  = new Pusher(PUSHER_KEY, { cluster: PUSHER_CLUSTER, encrypted: true });
const channel = pusher.subscribe('display-screen');

channel.bind('App\\Events\\QueueCalled', function(data) {
  updateDisplay(data.queue_number, data.visitor_name, data.loket_name);
  triggerTTS(data.queue_number, data.loket_name);
  flashScreen();
  updateNextList();
});

// ---- Update Layar ----
function updateDisplay(number, name, loket) {
  const servingEl = document.getElementById('now-serving');
  servingEl.innerHTML = `
    <div class="ns-label">🔊 Nomor Dipanggil</div>
    <div class="ns-number animate" id="ns-number">${number}</div>
    <div class="ns-name">${name}</div>
    <div class="ns-loket">${loket}</div>
  `;
  // Reset animation
  setTimeout(() => {
    const el = document.getElementById('ns-number');
    if (el) el.classList.remove('animate');
  }, 1000);
}

// ---- Text-to-Speech ----
function triggerTTS(number, loket) {
  if (!('speechSynthesis' in window)) return;

  window.speechSynthesis.cancel(); // stop current speech

  // Pisahkan karakter nomor agar dibaca per karakter (A-0-0-1)
  const numSpoken = number.split('').join('... ');
  const text = `Perhatian. Nomor antrian ${numSpoken}. Silakan menuju ${loket}.`;

  const utterance = new SpeechSynthesisUtterance(text);
  utterance.lang  = 'id-ID';
  utterance.rate  = 0.85;
  utterance.pitch = 1;
  utterance.volume = 1;

  // Delay 0.5 detik agar animasi layar lebih dulu
  setTimeout(() => window.speechSynthesis.speak(utterance), 500);

  // Ulangi sekali lagi setelah 5 detik
  setTimeout(() => {
    const u2 = new SpeechSynthesisUtterance(text);
    u2.lang = 'id-ID'; u2.rate = 0.85;
    window.speechSynthesis.speak(u2);
  }, 5000);
}

// ---- Flash Screen ----
function flashScreen() {
  const overlay = document.getElementById('flash-overlay');
  overlay.classList.remove('active');
  void overlay.offsetWidth; // reflow
  overlay.classList.add('active');
}

// ---- Update Daftar Berikutnya via Polling ----
function updateNextList() {
  fetch('/display/status')
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('next-list');
      if (!data.next || data.next.length === 0) {
        list.innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted)">Tidak ada antrian berikutnya</div>';
        return;
      }
      list.innerHTML = data.next.map((q, i) => `
        <div class="next-item">
          <div class="next-num">${q.queue_number}</div>
          <div class="next-info">
            <div class="next-name">${q.visitor_name}</div>
            <div class="next-pos">Urutan ke-${i + 1}</div>
          </div>
        </div>
      `).join('');
    }).catch(() => {});
}

// Refresh daftar next setiap 30 detik
setInterval(updateNextList, 30000);
</script>
</body>
</html>
