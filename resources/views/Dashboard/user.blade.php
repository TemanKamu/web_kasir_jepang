<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaisei POS - Manajemen User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('image/Logo.png') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#f0f4f8] font-sans" x-data="userManagement()">

    <div class="flex h-screen flex-col">
        <header class="bg-white border-b px-6 py-2 flex items-center justify-between h-20 shadow-sm z-10">
            <div class="flex items-center gap-3 w-48">
                <img src="{{ asset('image/Logo.png') }}" alt="Logo" class="h-12">
                <h1 class="text-3xl font-bold text-[#4a90e2]">Kaisei</h1>
            </div>

            <div class="flex-1 flex justify-start ml-10 gap-4">
                <a href="{{ route('menus.index') }}" class="flex flex-col items-center justify-center w-28 h-16 bg-white text-gray-400 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all">
                    <i class="fas fa-th-large text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">Dashboard</span>
                </a>
                <a href="/bills" class="flex flex-col items-center justify-center w-28 h-16 bg-white text-gray-400 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all">
                    <i class="fas fa-receipt text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">Bill</span>
                </a>
                <a href="/users" class="flex flex-col items-center justify-center w-28 h-16 bg-[#3EA1DC] text-white rounded-2xl shadow-md cursor-pointer transition-transform active:scale-95">
                    <i class="fas fa-user text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">User</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right mr-2">
                    <p class="text-xs font-bold text-gray-800">{{ Auth::user()->name ?? 'Admin Name' }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">Administrator</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-12 h-12 flex items-center justify-center bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h2 class="text-3xl font-black text-[#1e3a8a] tracking-tight">Manajemen User</h2>
                        <p class="text-gray-500 mt-1">Kelola hak akses dan informasi pengguna sistem Kaisei.</p>
                    </div>
                    <button @click="openModal('add')" class="bg-[#3EA1DC] text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 shadow-lg shadow-blue-100 hover:scale-105 transition-all">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah User Baru</span>
                    </button>
                    
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-16 h-16 bg-blue-50 text-[#3EA1DC] rounded-2xl flex items-center justify-center text-2xl">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em]">Total Admin</p>
                            <p class="text-2xl font-black text-gray-800">{{ $users->where('role_id', 1)->count() }} <span class="text-sm font-medium text-gray-400">Personil</span></p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-2xl">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em]">Total Customer</p>
                            <p class="text-2xl font-black text-gray-800">{{ $users->where('role_id', 2)->count() }} <span class="text-sm font-medium text-gray-400">Akun</span></p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('users.index') }}" method="GET" class="flex flex-wrap items-center justify-center gap-4 mb-8">
                    <div class="relative w-full max-w-md">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau Email..." 
                            class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-400 transition-all shadow-sm text-sm">
                    </div>

                    <div class="relative">
                        <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-400 transition-all shadow-sm text-sm font-bold text-gray-600">
                    </div>

                    <div class="relative">
                        <select name="role" onchange="this.form.submit()" 
                            class="pl-6 pr-10 py-3 bg-white border border-gray-100 rounded-2xl font-bold text-gray-600 outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm appearance-none cursor-pointer">
                            <option value="">Semua Role</option>
                            <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Customer</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-8 py-3 bg-[#3EA1DC] text-white rounded-2xl font-bold shadow-md hover:bg-blue-500 transition-all text-sm">
                            Cari
                        </button>
                        
                        @if(request()->anyFilled(['search', 'date', 'role']))
                            <a href="{{ route('users.index') }}" title="Reset Filter" class="px-5 py-3 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition-all text-sm flex items-center justify-center">
                                <i class="fas fa-redo text-xs"></i>
                            </a>
                        @endif
                    </div>
                </form>
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50 text-left">
                                <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">Nama User</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">Email</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">No. Telp</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Role</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="user in users" :key="user.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5 font-bold text-gray-700" x-text="user.name"></td>
                                    <td class="px-8 py-5 text-gray-500 text-sm font-mono" x-text="user.email"></td>
                                    <td class="px-8 py-5 text-gray-500 text-sm" x-text="user.phone_number"></td>
                                    <td class="px-8 py-5 text-center">
                                        <span :class="user.role_id == 1 ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600'" 
                                              class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                              x-text="user.role_id == 1 ? 'Admin' : 'Customer'"></span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openModal('edit', user)" class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-600 rounded-xl hover:bg-[#3EA1DC] hover:text-white transition-all"><i class="fas fa-edit text-xs"></i></button>
                                            <button @click="deleteUser(user.id)" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all"><i class="fas fa-trash text-xs"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden" @click.away="showModal = false">
            <div class="p-8">
                <h3 class="text-2xl font-black text-gray-800 mb-6" x-text="isEdit ? 'Update User' : 'Tambah User Baru'"></h3>
                
                <div class="space-y-5">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                        <input type="text" x-model="formData.name" :class="errors.name ? 'border-red-500' : 'border-gray-100'" 
                            class="w-full mt-1.5 p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-blue-400 outline-none transition-all">
                        <template x-if="errors.name">
                            <p class="text-red-500 text-[10px] mt-1 font-bold" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Address</label>
                        <input type="email" x-model="formData.email" :class="errors.email ? 'border-red-500' : 'border-gray-100'" 
                            class="w-full mt-1.5 p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-blue-400 outline-none transition-all">
                        <template x-if="errors.email">
                            <p class="text-red-500 text-[10px] mt-1 font-bold" x-text="errors.email[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Telepon</label>
                        <input type="text" x-model="formData.phone_number" :class="errors.phone_number ? 'border-red-500' : 'border-gray-100'" 
                            class="w-full mt-1.5 p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-blue-400 outline-none transition-all" placeholder="08xxxx">
                        <template x-if="errors.phone_number">
                            <p class="text-red-500 text-[10px] mt-1 font-bold" x-text="errors.phone_number[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="isEdit ? 'Password (Kosongkan jika tidak ganti)' : 'Password'"></label>
                        <input type="password" x-model="formData.password" :class="errors.password ? 'border-red-500' : 'border-gray-100'" 
                            class="w-full mt-1.5 p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-blue-400 outline-none transition-all">
                        <template x-if="errors.password">
                            <p class="text-red-500 text-[10px] mt-1 font-bold" x-text="errors.password[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Akses Role</label>
                        <select x-model="formData.role_id" :class="errors.role_id ? 'border-red-500' : 'border-gray-100'" 
                                class="w-full mt-1.5 p-4 bg-gray-50 border rounded-2xl outline-none appearance-none cursor-pointer">
                            <option value="1">Admin / Kasir</option>
                            <option value="2">Customer / Tablet</option>
                        </select>
                        <template x-if="errors.role_id">
                            <p class="text-red-500 text-[10px] mt-1 font-bold" x-text="errors.role_id[0]"></p>
                        </template>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button @click="showModal = false" class="flex-1 py-4 font-bold text-gray-400 hover:bg-gray-50 rounded-2xl transition-all">Batal</button>
                    <button @click="saveUser()" class="flex-1 py-4 bg-[#3EA1DC] text-white font-bold rounded-2xl shadow-lg shadow-blue-100 hover:bg-blue-600 transition-all">Simpan User</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userManagement() {
            return {
                showModal: false,
                isEdit: false,
                users: @json($users instanceof \Illuminate\Pagination\LengthAwarePaginator ? $users->items() : $users),
                errors: {}, // Tempat menyimpan pesan error validasi
                formData: { id: null, name: '', email: '', phone_number: '', password: '', role_id: 2 },

                openModal(type, user = null) {
                    this.errors = {}; // Reset error setiap buka modal
                    this.isEdit = type === 'edit';
                    if (this.isEdit) {
                        this.formData = { ...user, password: '' };
                    } else {
                        this.formData = { id: null, name: '', email: '', phone_number: '', password: '', role_id: 2 };
                    }
                    this.showModal = true;
                },

                async saveUser() {
                    const isEdit = !!this.formData.id;
                    const url = isEdit ? `/users/${this.formData.id}` : '/users';
                    
                    // Spoofing PUT untuk Route::resource jika sedang Edit
                    let payload = { ...this.formData };
                    if (isEdit) payload._method = 'PUT';

                    try {
                        const response = await fetch(url, {
                            method: 'POST', // Selalu POST, Laravel handle via _method
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            location.reload();
                        } else if (response.status === 422) {
                            this.errors = result.errors; // Masukkan error ke state
                        }
                    } catch (e) { console.error(e); }
                },
                async deleteUser(id) {
                    if (!confirm('Yakin ingin menghapus user ini?')) return;

                    try {
                        const response = await fetch(`/users/${id}`, {
                            method: 'DELETE', // Method DELETE untuk resource
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            location.reload();
                        }
                    } catch (error) {
                        console.error("Gagal menghapus:", error);
                    }
                }
            }
        }
    </script>
</body>
</html>