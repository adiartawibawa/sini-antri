<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Operator – {{ Auth::guard('operator')->user()->loket_name }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary: #1a56db;
    --primary-dark: #1343b3;
    --bg: #f1f5f9;
    --sidebar: #0f172a;
    --card: #ffffff;
    --text: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --green:  #059669;
    --yellow: #d97706;
    --red:    #dc2626;
  }
  body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
  }

  /* ---- Top Bar ---- */
  .topbar {
    background: var(--sidebar);
    color: white;
    padding: .875rem 1.5rem;
    display: flex; align-items: center; justify-content: space-between;
  }
  .topbar-brand { font-weight: 800; font-size: 1.1rem; display:flex; align-items:center; gap:.5rem; }
  .topbar-right { display:flex; align-items:center; gap:1rem; font-size:.875rem; }
  .loket-badge {
    background: var(--primary);
    padding: .3rem .75rem; border-radius: 999px;
    font-weight: 700; font-size: .8rem;
  }
  .logout-btn {
    background: none; border: 1px solid rgba(255,255,255,.3);
    color: white; padding: .3rem .75rem; border-radius: 8px;
    cursor: pointer; font-size: .8rem; transition: background .2s;
  }
  .logout-btn:hover { background: rgba(255,255,255,.1); }

  /* ---- Main Layout ---- */
  .main { display: grid; grid-template-columns: 1fr 380px; gap: 1.25rem; padding: 1.25rem; }
  @media(max-width: 900px) { .main { grid-template-columns: 1fr; } }

  /* ---- Cards ---- */
  .card {
    background: var(--card);
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    overflow: hidden;
  }
  .card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .card-title { font-weight: 700; font-size: 1rem; }
  .count-badge {
    background: var(--primary);
    color: white;
    font-size: .75rem; font-weight: 700;
    padding: .2rem .6rem; border-radius: 999px;
    min-width: 24px; text-align: center;
  }

  /* ---- Active Queue Box ---- */
  .active-box {
    padding: 1.5rem;
    text-align: center;
  }
  .active-label { font-size: .8rem; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
  .active-number {
    font-size: 5rem; font-weight: 900;
    color: var(--primary); line-height: 1;
    margin: .5rem 0;
  }
  .active-name { font-size: 1rem; font-weight: 600; color: var(--text); }
  .active-purpose { font-size: .85rem; color: var(--muted); margin-top: .25rem; }

  /* ---- Action Buttons ---- */
  .actions {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: .75rem; padding: .75rem 1.25rem 1.25rem;
  }
  .btn {
    padding: .65rem .5rem;
    border: none; border-radius: 10px;
    font-size: .82rem; font-weight: 700; cursor: pointer;
    transition: all .15s;
    display: flex; align-items: center; justify-content: center; gap: .3rem;
  }
  .btn:active { transform: scale(.97); }
  .btn-call    { background: var(--primary); color: white; grid-column: span 3; padding: 1rem; font-size: 1rem; }
  .btn-call:hover { background: var(--primary-dark); }
  .btn-recall  { background: #fffbeb; color: var(--yellow); border: 2px solid #fde68a; }
  .btn-skip    { background: #fff1f2; color: var(--red);    border: 2px solid #fecaca; }
  .btn-done    { background: #f0fdf4; color: var(--green);  border: 2px solid #6ee7b7; }
  .btn:disabled { opacity: .4; cursor: not-allowed; }

  /* ---- Queue List ---- */
  .queue-list { padding: 0; list-style: none; max-height: 520px; overflow-y: auto; }
  .queue-item {
    display: flex; align-items: center; gap: 1rem;
    padding: .875rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
  }
  .queue-item:last-child { border-bottom: none; }
  .queue-item:hover { background: #f8fafc; }
  .queue-item.new-item { animation: slideIn .4s ease; }
  @keyframes slideIn {
    from { background: #dbeafe; transform: translateX(-8px); }
    to   { background: transparent; transform: translateX(0); }
  }
  .q-number {
    font-size: 1.25rem; font-weight: 800; color: var(--primary);
    min-width: 60px;
  }
  .q-info { flex: 1; min-width: 0; }
  .q-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .q-purpose { font-size: .78rem; color: var(--muted); margin-top: .15rem; }
  .q-time { font-size: .75rem; color: var(--muted); white-space: nowrap; }

  .empty-state {
    text-align: center; padding: 3rem 1rem;
    color: var(--muted); font-size: .9rem;
  }
  .empty-state .emoji { font-size: 3rem; display: block; margin-bottom: .75rem; }

  /* ---- Stats Bar ---- */
  .stats-bar {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 1rem; margin-bottom: 1.25rem;
  }
  .stat-card {
    background: var(--card);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
  }
  .stat-value { font-size: 2rem; font-weight: 800; }
  .stat-label { font-size: .78rem; color: var(--muted); margin-top: .15rem; }
  .stat-card.blue  .stat-value { color: var(--primary); }
  .stat-card.green .stat-value { color: var(--green); }
  .stat-card.gray  .stat-value { color: var(--muted); }

  /* ---- Toast ---- */
  .toast {
    position: fixed; bottom: 1.5rem; right: 1.5rem;
    background: #1e293b; color: white;
    padding: .875rem 1.25rem; border-radius: 12px;
    font-size: .875rem; font-weight: 600;
    transform: translateY(100px); opacity: 0;
    transition: all .3s ease;
    z-index: 100; max-width: 320px;
  }
  .toast.show { transform: translateY(0); opacity: 1; }
  .toast.success { border-left: 4px solid #22c55e; }
  .toast.error   { border-left: 4px solid #ef4444; }

  /* ---- Connection Status ---- */
  .conn-dot {
    width: 8px; height: 8px; border-radius: 50%;
    display: inline-block; margin-right: .35rem;
  }
  .conn-dot.connected    { background: #22c55e; animation: livePulse 1.5s infinite; }
  .conn-dot.disconnected { background: #ef4444; }
  @keyframes livePulse {
    0%,100% { opacity:1; } 50% { opacity:.4; }
  }
</style>
</head>
<body>

<!-- Top Bar -->
<div class="topbar">
  <div class="topbar-brand">🖥️ Dashboard Operator</div>
  <div class="topbar-right">
    <span>
      <span class="conn-dot connected" id="conn-dot"></span>
      <span id="conn-label" style="font-size:.78rem;color:#94a3b8">Terhubung</span>
    </span>
    <span class="loket-badge">{{ Auth::guard('operator')->user()->loket_name }}</span>
    <span style="color:#94a3b8">{{ Auth::guard('operator')->user()->name }}</span>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
      @csrf
      <button class="logout-btn" type="submit">Keluar</button>
    </form>
  </div>
</div>

<!-- Main Content -->
<div class="main">

  <!-- Kolom Kanan: Antrian Aktif -->
  <div style="order:2; display:flex; flex-direction:column; gap:1.25rem;">

    <!-- Statistik -->
    <div class="stats-bar">
      <div class="stat-card blue">
        <div class="stat-value" id="stat-waiting">{{ $waitingQueues->count() }}</div>
        <div class="stat-label">⏳ Menunggu</div>
      </div>
      <div class="stat-card green">
        <div class="stat-value" id="stat-served">
          {{ \App\Models\Queue::where('status','completed')->whereDate('created_at',today())->count() }}
        </div>
        <div class="stat-label">✅ Selesai Hari Ini</div>
      </div>
      <div class="stat-card gray">
        <div class="stat-value" id="stat-skipped">
          {{ \App\Models\Queue::where('status','skipped')->whereDate('created_at',today())->count() }}
        </div>
        <div class="stat-label">⏭️ Dilewati</div>
      </div>
    </div>

    <!-- Panel Antrian Aktif -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">🎯 Sedang Dilayani</span>
        <span id="active-loket" style="font-size:.8rem;color:var(--muted)">{{ Auth::guard('operator')->user()->loket_name }}</span>
      </div>

      <div class="active-box" id="active-box">
        @if($activeQueue)
          <div class="active-label">Nomor Antrian</div>
          <div class="active-number" id="active-number">{{ $activeQueue->queue_number }}</div>
          <div class="active-name" id="active-name">{{ $activeQueue->visitor_name }}</div>
          <div class="active-purpose" id="active-purpose">{{ $activeQueue->purpose ?? '–' }}</div>
        @else
          <div style="padding:1.5rem 0;color:var(--muted)">
            <div style="font-size:3rem">🟢</div>
            <div style="margin-top:.5rem;font-weight:600">Siap Melayani</div>
            <div style="font-size:.85rem;margin-top:.25rem">Klik "Panggil" untuk memanggil antrian berikutnya</div>
          </div>
        @endif
      </div>

      <!-- Tombol Aksi -->
      <div class="actions">
        <button class="btn btn-call" id="btn-call" onclick="callQueue()">
          📢 Panggil Berikutnya
        </button>
        <button class="btn btn-recall" id="btn-recall"
          @if(!$activeQueue) disabled @endif
          onclick="recallQueue()">
          🔁 Panggil Ulang
        </button>
        <button class="btn btn-skip" id="btn-skip"
          @if(!$activeQueue) disabled @endif
          onclick="skipQueue()">
          ⏭️ Lewati
        </button>
        <button class="btn btn-done" id="btn-done"
          @if(!$activeQueue) disabled @endif
          onclick="completeQueue()">
          ✅ Selesai
        </button>
      </div>
    </div>
  </div>

  <!-- Kolom Kiri: Daftar Antrian Menunggu -->
  <div style="order:1;">
    <div class="card" style="height:100%">
      <div class="card-header">
        <span class="card-title">📋 Daftar Antrian Menunggu</span>
        <span class="count-badge" id="waiting-badge">{{ $waitingQueues->count() }}</span>
      </div>

      <ul class="queue-list" id="queue-list">
        @forelse($waitingQueues as $q)
          <li class="queue-item" id="queue-item-{{ $q->id }}" data-id="{{ $q->id }}">
            <span class="q-number">{{ $q->queue_number }}</span>
            <div class="q-info">
              <div class="q-name">{{ $q->visitor_name }}</div>
              <div class="q-purpose">{{ $q->purpose ?: 'Tidak ada keterangan' }}</div>
            </div>
            <span class="q-time">{{ $q->created_at->format('H:i') }}</span>
          </li>
        @empty
          <li class="empty-state" id="empty-state">
            <span class="emoji">🎉</span>
            Belum ada antrian yang menunggu
          </li>
        @endforelse
      </ul>
    </div>
  </div>

</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<script>
const PUSHER_KEY     = "{{ config('broadcasting.connections.pusher.key') }}";
const PUSHER_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').content;

let activeQueueId   = @json($activeQueue?->id);
let activeQueueData = @json($activeQueue);

// ---- Pusher Setup ----
const pusher = new Pusher(PUSHER_KEY, { cluster: PUSHER_CLUSTER, encrypted: true });
const channel = pusher.subscribe('operator-dashboard');

pusher.connection.bind('connected',    () => updateConn(true));
pusher.connection.bind('disconnected', () => updateConn(false));
pusher.connection.bind('error',        () => updateConn(false));

function updateConn(ok) {
  const dot   = document.getElementById('conn-dot');
  const label = document.getElementById('conn-label');
  dot.className = 'conn-dot ' + (ok ? 'connected' : 'disconnected');
  label.textContent = ok ? 'Terhubung' : 'Terputus';
}

// Antrian baru masuk
channel.bind('App\\Events\\QueueCreated', function(data) {
  addQueueItem(data);
  updateStats(data.waiting_count);
  showToast('🎟️ Antrian baru: ' + data.queue_number + ' – ' + data.visitor_name, 'success');
});

// Antrian dipanggil / status berubah
channel.bind('App\\Events\\QueueStatusChanged', function(data) {
  removeQueueItem(data.id);
  updateStats(data.waiting_count);
});

// ---- DOM Helpers ----
function addQueueItem(data) {
  const list  = document.getElementById('queue-list');
  const empty = document.getElementById('empty-state');
  if (empty) empty.remove();

  const li = document.createElement('li');
  li.className = 'queue-item new-item';
  li.id        = 'queue-item-' + data.id;
  li.dataset.id = data.id;
  li.innerHTML = `
    <span class="q-number">${data.queue_number}</span>
    <div class="q-info">
      <div class="q-name">${data.visitor_name}</div>
      <div class="q-purpose">${data.purpose || 'Tidak ada keterangan'}</div>
    </div>
    <span class="q-time">${data.created_at}</span>
  `;
  list.appendChild(li);
}

function removeQueueItem(id) {
  const el = document.getElementById('queue-item-' + id);
  if (el) {
    el.style.opacity = '0';
    el.style.transform = 'translateX(20px)';
    el.style.transition = 'all .3s';
    setTimeout(() => {
      el.remove();
      if (document.querySelectorAll('.queue-item').length === 0) {
        const list = document.getElementById('queue-list');
        list.innerHTML = `<li class="empty-state" id="empty-state"><span class="emoji">🎉</span>Belum ada antrian yang menunggu</li>`;
      }
    }, 300);
  }
}

function updateStats(waitingCount) {
  document.getElementById('stat-waiting').textContent  = waitingCount;
  document.getElementById('waiting-badge').textContent = waitingCount;
}

function updateActiveBox(queue, loketName) {
  const box = document.getElementById('active-box');
  if (queue) {
    box.innerHTML = `
      <div class="active-label">Nomor Antrian</div>
      <div class="active-number" id="active-number">${queue.queue_number}</div>
      <div class="active-name" id="active-name">${queue.visitor_name}</div>
      <div class="active-purpose" id="active-purpose">${queue.purpose || '–'}</div>
    `;
    document.getElementById('btn-recall').disabled = false;
    document.getElementById('btn-skip').disabled   = false;
    document.getElementById('btn-done').disabled   = false;
  } else {
    box.innerHTML = `
      <div style="padding:1.5rem 0;color:var(--muted)">
        <div style="font-size:3rem">🟢</div>
        <div style="margin-top:.5rem;font-weight:600">Siap Melayani</div>
        <div style="font-size:.85rem;margin-top:.25rem">Klik "Panggil" untuk memanggil antrian berikutnya</div>
      </div>`;
    document.getElementById('btn-recall').disabled = true;
    document.getElementById('btn-skip').disabled   = true;
    document.getElementById('btn-done').disabled   = true;
  }
}

// ---- API Actions ----
async function callQueue() {
  const btn = document.getElementById('btn-call');
  btn.disabled = true;
  btn.textContent = '⏳ Memproses...';

  try {
    const res  = await post('{{ route("operator.queue.call") }}', {});
    const data = await res.json();

    if (res.ok) {
      activeQueueId   = data.queue.id;
      activeQueueData = data.queue;
      updateActiveBox(data.queue, data.loket_name);
      removeQueueItem(data.queue.id);
      showToast('📢 Memanggil ' + data.queue_number, 'success');
    } else {
      showToast(data.message, 'error');
    }
  } catch(e) {
    showToast('Gagal terhubung ke server.', 'error');
  }

  btn.disabled    = false;
  btn.innerHTML   = '📢 Panggil Berikutnya';
}

async function recallQueue() {
  if (!activeQueueId) return;
  const res  = await post(`/operator/queue/${activeQueueId}/recall`, {});
  const data = await res.json();
  showToast(res.ok ? '🔁 ' + data.message : data.message, res.ok ? 'success' : 'error');
}

async function skipQueue() {
  if (!activeQueueId) return;
  if (!confirm('Lewati antrian ini? Pengunjung tidak akan dilayani.')) return;

  const res  = await post(`/operator/queue/${activeQueueId}/skip`, {});
  const data = await res.json();

  if (res.ok) {
    activeQueueId = null;
    activeQueueData = null;
    updateActiveBox(null);
    updateStats(data.waiting_count);
    showToast('⏭️ Antrian dilewati.', 'success');
  }
}

async function completeQueue() {
  if (!activeQueueId) return;

  const res  = await post(`/operator/queue/${activeQueueId}/complete`, {});
  const data = await res.json();

  if (res.ok) {
    activeQueueId = null;
    activeQueueData = null;
    updateActiveBox(null);
    updateStats(data.waiting_count);
    // Increment selesai counter
    const el = document.getElementById('stat-served');
    el.textContent = parseInt(el.textContent) + 1;
    showToast('✅ Antrian selesai dilayani.', 'success');
  }
}

function post(url, data) {
  return fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
    body: JSON.stringify(data),
  });
}

// ---- Toast ----
let toastTimer;
function showToast(msg, type = 'success') {
  const el = document.getElementById('toast');
  el.textContent  = msg;
  el.className    = 'toast show ' + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('show'), 3500);
}
</script>
</body>
</html>
