<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuota Penuh - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 text-center">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100">
        <div class="text-6xl mb-6">🛑</div>
        <h1 class="text-2xl font-black text-slate-900 mb-3">Kuota Sudah Penuh</h1>
        <p class="text-slate-500 leading-relaxed">Maaf, batas maksimal antrian untuk hari ini telah tercapai (Batas: {{ $setting->max_queue_limit }}). Silakan hubungi petugas atau kembali lagi besok.</p>
        <div class="mt-8 pt-8 border-t border-slate-100">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Sistem Antrian Digital</p>
        </div>
    </div>
</body>
</html>
