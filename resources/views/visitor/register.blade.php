<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Nomor Antrian</title>
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

<body class="bg-[#fef2f2] min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-[#b10303] rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-ticket text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Ambil Nomor Antrian</h1>
            <p class="text-sm text-[#64748b] mt-1">Isi data di bawah untuk mendapatkan nomor antrian</p>
            <span
                class="inline-flex items-center gap-1 bg-red-50 text-[#b10303] border border-red-100 rounded-full px-3 py-1 text-xs font-semibold mt-3">
                <i class="fa-solid fa-location-dot text-xs"></i> Lokasi: {{ strtoupper($locationCode) }}
            </span>
        </div>

        <!-- Stats Cards -->
        <div class="bg-slate-50 border border-[#e2e8f0] rounded-lg p-4 mb-6 flex gap-4 text-center">
            <div class="flex-1">
                <div class="text-3xl font-extrabold text-[#b10303]" id="waiting-count">{{ $waitingCount }}</div>
                <div class="text-xs text-[#64748b]">Menunggu</div>
            </div>
            <div class="flex-1">
                <div class="text-3xl font-extrabold text-[#b10303]">{{ $setting?->avg_service_minutes ?? 5 }} <span
                        class="text-sm">mnt</span></div>
                <div class="text-xs text-[#64748b]">Est. per orang</div>
            </div>
            <div class="flex-1">
                <div class="text-3xl font-extrabold text-[#b10303]">{{ $setting?->prefix ?? 'A' }}</div>
                <div class="text-xs text-[#64748b]">Seri Antrian</div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('visitor.take') }}" method="POST" id="queue-form">
            @csrf
            <input type="hidden" name="location_code" value="{{ $locationCode }}">

            <label class="block text-sm font-semibold text-[#1e293b] mb-1">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" id="visitor_name" name="visitor_name" placeholder="Masukkan nama Anda"
                value="{{ old('visitor_name') }}" required autofocus
                class="w-full px-4 py-3 border-2 border-[#e2e8f0] rounded-lg text-[#1e293b] focus:outline-none focus:border-[#b10303] focus:ring-3 focus:ring-[#b10303]/10 mb-4 transition">
            @error('visitor_name')
                <div class="text-red-600 text-xs -mt-3 mb-3">{{ $message }}</div>
            @enderror

            <label class="block text-sm font-semibold text-[#1e293b] mb-1">Keperluan</label>
            <textarea id="purpose" name="purpose" placeholder="Contoh: Pembayaran tagihan, Pengambilan dokumen..."
                class="w-full px-4 py-3 border-2 border-[#e2e8f0] rounded-lg text-[#1e293b] focus:outline-none focus:border-[#b10303] focus:ring-3 focus:ring-[#b10303]/10 mb-5 transition resize-y min-h-[80px]">{{ old('purpose') }}</textarea>

            <button type="submit"
                class="btn w-full bg-[#b10303] hover:bg-[#8b0202] text-white font-bold py-3 rounded-lg transition-all active:scale-95 flex items-center justify-center gap-2"
                id="submit-btn">
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
