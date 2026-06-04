<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Operator - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-height-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-slate-900 text-white p-6">
            <div class="text-2xl font-bold mb-8 flex items-center gap-2">
                <span class="bg-blue-600 p-1 rounded">🎟️</span> AdminPanel
            </div>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Dashboard</a>
                <a href="{{ route('admin.operators') }}" class="block px-4 py-2.5 rounded transition bg-blue-600">Manajemen Operator</a>
                <a href="{{ route('admin.settings') }}" class="block px-4 py-2.5 rounded transition hover:bg-slate-800">Pengaturan Antrian</a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-extrabold">Data Operator</h1>
                <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">+ Tambah Operator</button>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="p-4 font-bold text-slate-500">Nama</th>
                            <th class="p-4 font-bold text-slate-500">Email</th>
                            <th class="p-4 font-bold text-slate-500">Loket</th>
                            <th class="p-4 font-bold text-slate-500">Status</th>
                            <th class="p-4 font-bold text-slate-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operators as $op)
                        <tr class="border-b hover:bg-slate-50 transition">
                            <td class="p-4 font-semibold">{{ $op->name }}</td>
                            <td class="p-4 text-slate-500">{{ $op->email }}</td>
                            <td class="p-4"><span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">{{ $op->loket_name }}</span></td>
                            <td class="p-4">
                                @if($op->is_active)
                                    <span class="text-green-600 flex items-center gap-1.5 font-medium"><span class="w-2 h-2 bg-green-600 rounded-full"></span> Aktif</span>
                                @else
                                    <span class="text-slate-400 flex items-center gap-1.5 font-medium"><span class="w-2 h-2 bg-slate-400 rounded-full"></span> Nonaktif</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button onclick='openEditModal(@json($op))' class="text-blue-600 font-bold hover:underline">Edit</button>
                                <form action="{{ route('admin.operators.delete', $op->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus operator ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Add -->
    <div id="modal-add" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
            <h2 class="text-2xl font-bold mb-6">Tambah Operator Baru</h2>
            <form action="{{ route('admin.operators.store') }}" method="POST" class="space-y-4">
                @csrf
                <div><label class="block text-sm font-bold mb-1">Nama Lengkap</label><input type="text" name="name" required class="w-full p-2.5 border rounded-xl"></div>
                <div><label class="block text-sm font-bold mb-1">Email</label><input type="email" name="email" required class="w-full p-2.5 border rounded-xl"></div>
                <div><label class="block text-sm font-bold mb-1">Password</label><input type="password" name="password" required class="w-full p-2.5 border rounded-xl"></div>
                <div><label class="block text-sm font-bold mb-1">Nama Loket</label><input type="text" name="loket_name" required placeholder="Contoh: Loket 1" class="w-full p-2.5 border rounded-xl"></div>
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="this.closest('#modal-add').classList.add('hidden')" class="flex-1 px-4 py-2.5 border rounded-xl font-bold">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modal-edit" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
            <h2 class="text-2xl font-bold mb-6">Edit Operator</h2>
            <form id="edit-form" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div><label class="block text-sm font-bold mb-1">Nama Lengkap</label><input type="text" name="name" id="edit-name" required class="w-full p-2.5 border rounded-xl"></div>
                <div><label class="block text-sm font-bold mb-1">Email</label><input type="email" name="email" id="edit-email" required class="w-full p-2.5 border rounded-xl"></div>
                <div><label class="block text-sm font-bold mb-1">Password Baru (Kosongkan jika tidak ganti)</label><input type="password" name="password" class="w-full p-2.5 border rounded-xl"></div>
                <div><label class="block text-sm font-bold mb-1">Nama Loket</label><input type="text" name="loket_name" id="edit-loket" required class="w-full p-2.5 border rounded-xl"></div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="edit-active" value="1" class="w-4 h-4 rounded">
                    <label for="edit-active" class="text-sm font-bold">Status Aktif</label>
                </div>
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="flex-1 px-4 py-2.5 border rounded-xl font-bold">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold">Update</button>
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
</body>
</html>
