@extends('layouts.admin')

@section('title', 'Manajemen Operator')
@section('header_title', 'Manajemen Operator')
@section('header_subtitle', 'Kelola petugas loket dan akses sistem')

@section('header_actions')
    <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
        class="bg-[#b10303] text-white px-5 py-2.5 rounded-md font-bold shadow-lg shadow-[#b10303]/20 hover:bg-[#8b0202] transition flex items-center gap-2">
        <i class="fa-solid fa-plus text-xs"></i> Tambah Operator
    </button>
@endsection

@section('content')
    <div class="bg-white rounded-md shadow-sm border border-[#e2e8f0] overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-[#fef2f2] border-b border-[#e2e8f0]">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                        Informasi Operator</th>
                    <th class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                        Penempatan</th>
                    <th class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest">
                        Status</th>
                    <th
                        class="px-6 py-4 text-[10px] font-black text-[#64748b] uppercase tracking-widest text-right">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e2e8f0]">
                @forelse($operators as $op)
                    <tr class="hover:bg-[#fef2f2] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-md bg-[#fef2f2] flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-user text-[#b10303]"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-[#1e293b]">{{ $op->name }}</div>
                                    <div class="text-xs text-[#64748b] font-medium">{{ $op->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 bg-red-50 text-[#b10303] rounded-md text-[10px] font-black uppercase tracking-wider border border-red-100">
                                <i class="fa-solid fa-building mr-1"></i> {{ $op->loket_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($op->is_active)
                                <span
                                    class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-tight">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <i class="fa-regular fa-circle-check"></i> Aktif
                                </span>
                            @else
                                <span
                                    class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-tight">
                                    <span class="w-2 h-2 bg-slate-300 rounded-full"></span>
                                    <i class="fa-solid fa-circle-pause"></i> Off
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <button onclick='openEditModal(@json($op))'
                                class="text-[#b10303] font-bold text-xs uppercase tracking-widest hover:underline">
                                <i class="fa-regular fa-pen-to-square mr-1"></i> Edit
                            </button>
                            <form action="{{ route('admin.operators.delete', $op->id) }}" method="POST"
                                class="inline" onsubmit="return confirm('Hapus operator ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-[#64748b] font-bold text-xs uppercase tracking-widest hover:text-rose-600 transition-colors">
                                    <i class="fa-regular fa-trash-can mr-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-[#64748b] italic font-medium">
                            <i class="fa-regular fa-users-slash text-3xl block mb-2"></i>
                            Belum ada data operator
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Add -->
    <div id="modal-add"
        class="fixed inset-0 bg-[#1e293b]/60 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-md w-full max-w-md p-8 shadow-2xl border border-white/20 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-50 text-[#b10303] rounded-md flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h2 class="text-xl font-black text-[#1e293b] tracking-tight">TAMBAH OPERATOR</h2>
            </div>
            <form action="{{ route('admin.operators.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-regular fa-user mr-1"></i> Nama Lengkap
                    </label>
                    <input type="text" name="name" required
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-regular fa-envelope mr-1"></i> Email
                    </label>
                    <input type="email" name="email" required
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-lock mr-1"></i> Password
                    </label>
                    <input type="password" name="password" required
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-building mr-1"></i> Nama Loket
                    </label>
                    <input type="text" name="loket_name" required placeholder="Contoh: Loket 1"
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="this.closest('#modal-add').classList.add('hidden')"
                        class="flex-1 px-4 py-3 border border-[#e2e8f0] rounded-md font-bold text-[#64748b] hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-[#b10303] text-white rounded-md font-bold hover:bg-[#8b0202] transition shadow-lg shadow-[#b10303]/20">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modal-edit"
        class="fixed inset-0 bg-[#1e293b]/60 backdrop-blur-sm flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-md w-full max-w-md p-8 shadow-2xl border border-white/20 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-50 text-[#b10303] rounded-md flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <h2 class="text-xl font-black text-[#1e293b] tracking-tight">EDIT OPERATOR</h2>
            </div>
            <form id="edit-form" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-regular fa-user mr-1"></i> Nama Lengkap
                    </label>
                    <input type="text" name="name" id="edit-name" required
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-regular fa-envelope mr-1"></i> Email
                    </label>
                    <input type="email" name="email" id="edit-email" required
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-lock mr-1"></i> Password Baru
                    </label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#64748b] uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-building mr-1"></i> Nama Loket
                    </label>
                    <input type="text" name="loket_name" id="edit-loket" required
                        class="w-full p-3 border border-[#e2e8f0] rounded-md focus:border-[#b10303] focus:ring-0 outline-none transition">
                </div>
                <div class="flex items-center gap-3 p-3 bg-[#fef2f2] rounded-md border border-[#e2e8f0]">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="edit-active" value="1"
                        class="w-5 h-5 rounded text-[#b10303] focus:ring-0 cursor-pointer">
                    <label for="edit-active" class="text-sm font-bold text-[#1e293b] cursor-pointer">
                        <i class="fa-regular fa-circle-check mr-1"></i> Status Akun Aktif
                    </label>
                </div>
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="flex-1 px-4 py-3 border border-[#e2e8f0] rounded-md font-bold text-[#64748b] hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-[#b10303] text-white rounded-md font-bold hover:bg-[#8b0202] transition shadow-lg shadow-[#b10303]/20">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
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
@endpush
