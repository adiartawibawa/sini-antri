<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QR Code Antrian</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: #f0f4ff;
    display: flex; align-items: center; justify-content: center;
    min-height: 100vh; padding: 2rem;
  }
  .container { text-align: center; }
  .card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 12px 40px rgba(26,86,219,.15);
    padding: 3rem 2.5rem;
    display: inline-block;
    min-width: 340px;
  }
  h1 { font-size: 1.5rem; color: #1e293b; font-weight: 800; margin-bottom: .25rem; }
  .sub { color: #64748b; font-size: .9rem; margin-bottom: 2rem; }
  .qr-wrapper {
    border: 4px solid #1a56db;
    border-radius: 16px;
    padding: 1rem;
    display: inline-block;
    margin: 1rem 0;
  }
  .url-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: .75rem 1rem;
    font-size: .8rem;
    word-break: break-all;
    color: #1a56db;
    margin-top: 1rem;
    font-family: monospace;
  }
  .btn-print {
    display: inline-block;
    margin-top: 1.5rem;
    padding: .75rem 2rem;
    background: #1a56db;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
  }
  @media print {
    body { background: white; }
    .btn-print { display: none; }
  }
</style>
</head>
<body>
<div class="container">
  <div class="card">
    <h1>🎟️ Ambil Nomor Antrian</h1>
    <p class="sub">Pindai QR Code di bawah dengan kamera HP Anda</p>

    <div class="qr-wrapper">
      {!! QrCode::size(220)->style('round')->eye('circle')->color(26,86,219)->generate($url) !!}
    </div>

    <p style="font-size:.8rem;color:#64748b;margin-top:1rem">
      Lokasi: <strong>{{ strtoupper($locationCode) }}</strong>
    </p>
    <div class="url-box">{{ $url }}</div>

    <br>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak QR Code</button>
  </div>
</div>
</body>
</html>
