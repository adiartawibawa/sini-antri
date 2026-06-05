<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditutup - Sini Antri</title>
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
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#fef2f2] min-h-screen flex items-center justify-center p-6 text-center">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-[#e2e8f0]">
        <div class="text-6xl mb-6">
            <i class="fa-solid fa-lock text-[#b10303]"></i>
        </div>
        <h1 class="text-2xl font-black text-[#1e293b] mb-3">Pendaftaran Ditutup</h1>
        <p class="text-[#64748b] leading-relaxed">Maaf, pendaftaran antrian sedang tidak menerima kunjungan baru saat
            ini. Silakan kembali lagi nanti.</p>
        <div class="mt-8 pt-8 border-t border-[#e2e8f0]">
            <p class="text-xs text-[#64748b] font-bold uppercase tracking-widest">
                <i class="fa-solid fa-ticket-alt mr-1"></i> Sistem Antrian Digital
            </p>
        </div>
    </div>
</body>

</html>
