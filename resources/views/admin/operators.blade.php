<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Operator - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-link.active { background-color: #b10303; color: white; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-72 bg-slate-900 text-white flex flex-col shadow-xl z-20">
            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#b10303] rounded-md flex items-center justify-center text-xl shadow-lg shadow-[#b10303]/20">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black tracking-tight uppercase">Sini <span class="text-[#b10303]">Antri</span></h2>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Admin Control</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-chart-line"></i></span> Dashboard
                </a>
                <a href="{{ route('admin.operators') }}" class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-md font-semibold transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-users"></i></span> Operator
                </a>
                <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-gear"></i></span> Pengaturan
                </a>
                <a href="{{ route('admin.qrcode') }}" target="_blank" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition group">
                    <span class="text-lg w-8 text-center"><i class="fa-solid fa-print"></i></span> Cetak QR
                </a>
            </nav>

            <div class="p-4 mt-auto border-t border-white/5">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-md font-semibold text-rose-400 hover:bg-rose-500/10 transition">
                        <span class="text-lg w-8 text-center"><i class="fa-solid fa-right-from-bracket"></i></span> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-20 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-10">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Manajemen Operator</h1>
                    <p class="text-sm text-slate-500 font-medium">Kelola petugas loket dan akses sistem</p>
                </div>
                <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-[#b10303] text-white px-5 py-2.5 rounded-md font-bold shadow-lg shadow-[#b10303]/20 hover:bg-[#8b0202] transition flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Operator
                </button>
            </header>

            <div class="p-8">
                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-md shadow-sm mb-8 flex items-center gap-3 animate-fade-in">
                        <span class="text-xl"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="bg-white rounded-md shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi Operator</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Penempatan</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($operators as $op)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-md bg-slate-100 flex items-center justify-center text-lg text-slate-500">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $op->name }}</div>
                                                <div class="text-xs text-slate-500 font-medium">{{ $op->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-red-50 text-[#b10303] rounded-md text-[10px] font-black uppercase tracking-wider border border-red-100">
                                            {{ $op->loket_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($op->is_active)
                                            <span class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-tight">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Aktif
                                            </span>
                                        @else
                                            <span class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-tight">
                                                <span class="w-2 h-2 bg-slate-300 rounded-full"></span> Off
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3">
                                        <button onclick='openEditModal(@json($op))' class="text-[#b10303] font-bold text-xs uppercase tracking-widest hover:underline">Edit</button>
                                        <form action="{{ route('admin.operators.delete', $op->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus operator ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-rose-600 transition-colors">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic font-medium">
                                        Belum ada data operator
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Add -->
    <div id="modal-add" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-md w-full max-w-md p-8 shadow-2xl border border-white/20 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-50 text-[#b10303] rounded-md flex items-center justify-center text-xl text-[#b10303]">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">TAMBAH OPERATOR</h2>
            </div>
            <form action="{{ route('admin.operators.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email</label>
                    <input type="email" name="email" required class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Password</label>
                    <input type="password" name="password" required class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Loket</label>
                    <input type="text" name="loket_name" required placeholder="Contoh: Loket 1" class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="this.closest('#modal-add').classList.add('hidden')" class="flex-1 px-4 py-3 border border-slate-200 rounded-md font-bold text-slate-500 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-[#b10303] text-white rounded-md font-bold hover:bg-[#8b0202] transition shadow-lg shadow-[#b10303]/20">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modal-edit" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-md w-full max-w-md p-8 shadow-2xl border border-white/20 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-50 text-[#b10303] rounded-md flex items-center justify-center text-xl text-[#b10303]">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">EDIT OPERATOR</h2>
            </div>
            <form id="edit-form" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit-name" required class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email</label>
                    <input type="email" name="email" id="edit-email" required class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Loket</label>
                    <input type="text" name="loket_name" id="edit-loket" required class="w-full p-3 border border-slate-200 rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-md border border-slate-100">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="edit-active" value="1" class="w-5 h-5 rounded text-[#b10303] focus:ring-0">
                    <label for="edit-active" class="text-sm font-bold text-slate-700">Status Akun Aktif</label>
                </div>
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="flex-1 px-4 py-3 border border-slate-200 rounded-md font-bold text-slate-500 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-[#b10303] text-white rounded-md font-bold hover:bg-[#8b0202] transition shadow-lg shadow-[#b10303]/20">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(op) {
            document.getElementById('edit-form').action = '/admin/operators/' + op.id;
            document.getElementById('edit-name').value = op.name;
            document.getElementById('edit-email').value = op.email;
            document.getElementById('edit-loket').value = op.loket_name;
            document.getElementById('edit-active').checked = !!op.is_active;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</body>
</html>
