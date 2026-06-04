<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ambil Nomor Antrian</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary: #b10303;
    --primary-dark: #8b0202;
    --bg: #fef2f2;
    --card: #ffffff;
    --text: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --success: #059669;
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
    border-radius: 6px;
    box-shadow: 0 10px 40px rgba(177,3,3,.08);
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 420px;
  }
  .header {
    text-align: center;
    margin-bottom: 2rem;
  }
  .logo {
    width: 64px; height: 64px;
    background: var(--primary);
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
    font-size: 28px;
    color: white;
  }
  h1 { font-size: 1.5rem; color: var(--text); font-weight: 700; }
  .subtitle { color: var(--muted); font-size: .9rem; margin-top: .25rem; }
  .badge {
    display: inline-block;
    background: #fef2f2;
    color: var(--primary);
    border: 1px solid #fee2e2;
    border-radius: 999px;
    padding: .25rem .75rem;
    font-size: .8rem;
    font-weight: 600;
    margin-top: .75rem;
  }
  .stats {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 1rem;
    text-align: center;
  }
  .stat { flex: 1; }
  .stat-number { font-size: 1.75rem; font-weight: 800; color: var(--primary); }
  .stat-label { font-size: .75rem; color: var(--muted); }
  label {
    display: block;
    font-size: .875rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: .4rem;
  }
  input, textarea, select {
    width: 100%;
    padding: .75rem 1rem;
    border: 2px solid var(--border);
    border-radius: 6px;
    font-size: 1rem;
    color: var(--text);
    background: white;
    transition: border-color .2s;
    margin-bottom: 1.25rem;
    font-family: inherit;
  }
  input:focus, textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(177,3,3,.1);
  }
  textarea { resize: vertical; min-height: 80px; }
  .btn {
    width: 100%;
    padding: .875rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
  }
  .btn:hover { background: var(--primary-dark); }
  .btn:active { transform: scale(.98); }
  .btn:disabled { background: #94a3b8; cursor: not-allowed; }
  .error {
    color: #dc2626;
    font-size: .8rem;
    margin-top: -.9rem;
    margin-bottom: .75rem;
  }
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <div class="logo"><i class="fa-solid fa-ticket"></i></div>
    <h1>Ambil Nomor Antrian</h1>
    <p class="subtitle">Isi data di bawah untuk mendapatkan nomor antrian</p>
    <span class="badge"><i class="fa-solid fa-location-dot"></i> Lokasi: {{ strtoupper($locationCode) }}</span>
  </div>

  <div class="stats">
    <div class="stat">
      <div class="stat-number" id="waiting-count">{{ $waitingCount }}</div>
      <div class="stat-label">Menunggu</div>
    </div>
    <div class="stat">
      <div class="stat-number">{{ $setting?->avg_service_minutes ?? 5 }} mnt</div>
      <div class="stat-label">Est. per orang</div>
    </div>
    <div class="stat">
      <div class="stat-number">{{ $setting?->prefix ?? 'A' }}</div>
      <div class="stat-label">Seri Antrian</div>
    </div>
  </div>

  <form action="{{ route('visitor.take') }}" method="POST" id="queue-form">
    @csrf
    <input type="hidden" name="location_code" value="{{ $locationCode }}">

    <label for="visitor_name">Nama Lengkap <span style="color:red">*</span></label>
    <input type="text" id="visitor_name" name="visitor_name"
           placeholder="Masukkan nama Anda"
           value="{{ old('visitor_name') }}" required autofocus>
    @error('visitor_name')
      <div class="error">{{ $message }}</div>
    @enderror

    <label for="purpose">Keperluan</label>
    <textarea id="purpose" name="purpose"
              placeholder="Contoh: Pembayaran tagihan, Pengambilan dokumen...">{{ old('purpose') }}</textarea>

    <button type="submit" class="btn" id="submit-btn">
      <i class="fa-solid fa-ticket"></i> Ambil Nomor Antrian
    </button>
  </form>
</div>

<script>
document.getElementById('queue-form').addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
});
</script>
</body>
</html>
