<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaisei POS - Full Layout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        [x-cloak] { display: none !important; }
        
    </style>
</head>
<body class="bg-[#f0f4f8] font-sans text-gray-800"
  x-data="{ 
    search: '',
    openAdd: false, 
    openDetail: false, 
    openEdit: false,
    openAddCategory: false, 
    openAddSub: false, 
    selectedMenu: {}, 
    editData: {},
    openEditCategory: false,
    editCategoryData: {},
    imagePreviewCategory: null,
    openEditSub: false,
    editSubData: { name: '', id: '', category_id: '' }, // Beri default object

    // Helper untuk ambil CSRF secara aman
    getCsrf() {
        const tag = document.querySelector('meta[name=csrf-token]');
        return tag ? tag.getAttribute('content') : '';
    },

    async toggleStatus(id) {
        try {
            const response = await fetch(`/menus/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Update state di frontend agar tombol/label berubah seketika
                if (this.selectedMenu && this.selectedMenu.id == id) {
                    this.selectedMenu.status = data.new_status;
                }

                // Kirim notifikasi
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { 
                        message: `Status menu sekarang: ${data.new_status.toUpperCase()}`, 
                        type: 'success' 
                    } 
                }));

            } else {
                throw new Error(data.message || 'Server Error 500');
            }
        } catch (error) {
            console.error('Error:', error);
            // Ganti alert dengan notifikasi error agar konsisten
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { message: error.message, type: 'error' } 
            }));
        }
    },

    openEditMenu(menu) {
        this.openDetail = false;
        this.editData = { ...menu }; 
        this.openEdit = true;
    },

    async submitUpdate(event) {
        const formData = new FormData(event.target);
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/menus/${this.editData.id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrf()
                }
            });

            if (response.ok) {
                this.openEdit = false; // Tutup dulu modalnya
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Status Berhasil Diubah!', type: 'success' } 
                }));
            } else {
                alert('Gagal update data');
            }
        } catch (error) {
            console.error(error);
        }
    },

    async confirmDelete(id) {
        if (!confirm('Yakin mau hapus menu ini?')) return;

        try {
            const response = await fetch(`/menus/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrf()
                }
            });
            const data = await response.json();
           if (data.success) {
                this.openDetail = false; // Tutup dulu detailnya
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Menu Berhasil Dihapus!', type: 'success' } 
                }));
            } else {
                alert('Gagal menghapus menu');
            }
        } catch (error) {
            console.error(error);
        }
    }
}"
 @close-modal-add.window="openAdd = false">
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success'
        }" 
        x-init="
            @if(session('success'))
                message = '{{ session('success') }}';
                type = 'success';
                show = true;
                setTimeout(() => show = false, 4000);
            @endif
            @if($errors->any())
                message = '{{ $errors->first() }}';
                type = 'error';
                show = true;
                setTimeout(() => show = false, 4000);
            @endif
             window.addEventListener('notify', e => {
                message = e.detail.message;
                type = e.detail.type;
                show = true;
                setTimeout(() => show = false, 4000);
            });
        "
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        class="fixed bottom-5 right-5 z-[200] min-w-[300px]"
        x-cloak>
        
        <div :class="type === 'success' ? 'bg-emerald-500' : 'bg-red-500'" 
            class="text-white px-6 py-4 rounded-3xl shadow-2xl flex items-center justify-between gap-4 border-4 border-white">
            
            <div class="flex items-center gap-3">
                <template x-if="type === 'success'">
                    <i class="fas fa-check-circle text-xl"></i>
                </template>
                <template x-if="type === 'error'">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </template>
                <span x-text="message" class="font-bold uppercase text-xs tracking-wider"></span>
            </div>

            <button @click="show = false" class="hover:scale-125 transition-transform">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    {{-- Pop up Section --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data" 
            @submit.prevent="submitForm($event)"
            class="bg-white w-full max-w-lg rounded-[3rem] p-10 shadow-2xl transition-all"
            x-data="{ 
                imagePreview: null,
                handleFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imagePreview = URL.createObjectURL(file);
                    }
                },
                async submitForm(event) {
                    const formData = new FormData(event.target);
                    try {
                        const response = await fetch(event.target.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            // Tutup Modal
                            this.openAdd = false; 
                            
                            // Trigger Notifikasi
                            window.dispatchEvent(new CustomEvent('notify', { 
                                detail: { message: data.message || 'Berhasil!', type: 'success' } 
                            }));

                            // OPTIONAL: Reload hanya setelah toast selesai (misal 3 detik)
                            // setTimeout(() => window.location.reload(), 3000); 
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            }"
            @submit.prevent="submitForm($event)">
            @csrf
            <h2 class="text-3xl font-black text-[#1e3a8a] text-center mb-8 uppercase tracking-tight">Tambah Menu Baru</h2>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Nama Menu</label>
                    <input type="text" name="name" required placeholder="Contoh: Shoyu Ramen" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Kategori</label>
                        <select name="sub_category_id" required class="...">
                            <option value="">Pilih Sub-Kategori</option>
                            @foreach($groupedCategories as $main)
                                <optgroup label="{{ $main->name }}">
                                    @foreach($main->subCategories as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Harga (Rp)</label>
                        <input type="number" name="price" required placeholder="15000" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Deskripsi</label>
                    <textarea name="desc" rows="2" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold" placeholder="Jelaskan menu lu..."></textarea>
                </div>

                <input type="hidden" name="status" value="available">

                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Foto Menu</label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-3xl p-4 flex flex-col items-center justify-center text-gray-400 hover:bg-gray-50 cursor-pointer transition-colors group h-40 overflow-hidden">
                        <template x-if="!imagePreview">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-plus-circle text-4xl mb-2 group-hover:text-blue-500"></i>
                                <span class="font-bold">Klik untuk Upload</span>
                            </div>
                        </template>
                        
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover rounded-3xl">
                        </template>

                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleFile($event)" accept="image/*">
                    </div>
                </div>
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                        <ul class="list-disc list-inside text-sm text-red-600 font-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 mt-10">
                <button type="button" @click="openAdd = false" class="py-4 border-2 border-blue-500 text-blue-500 rounded-2xl font-black uppercase hover:bg-blue-50 transition-colors">Batal</button>
                <button type="submit" class="py-4 bg-[#3b82f6] text-white rounded-2xl font-black uppercase shadow-lg shadow-blue-100 hover:bg-blue-600 transition-colors">Simpan Menu</button>
            </div>
        </form>
    </div>
    <div x-show="openAddCategory" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" 
            class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl transition-all"
            x-data="{ 
                imagePreviewCategory: null
            }">
            @csrf
            <h2 class="text-3xl font-black text-[#1e3a8a] text-center mb-8 uppercase tracking-tight">Kategori Baru</h2>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Misal: Makanan, Minuman" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Icon Kategori</label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-3xl p-6 flex flex-col items-center justify-center text-gray-400 hover:bg-gray-50 cursor-pointer transition-colors group min-h-[160px]">
                        
                        <template x-if="!imagePreviewCategory">
                            <div class="text-center">
                                <i class="fas fa-image text-3xl mb-2 group-hover:text-blue-500"></i>
                                <p class="font-bold text-xs uppercase">Upload Icon</p>
                            </div>
                        </template>

                        <template x-if="imagePreviewCategory">
                            <div class="relative w-full h-full flex flex-col items-center">
                                <img :src="imagePreviewCategory" class="w-20 h-20 object-contain rounded-xl mb-2 shadow-md">
                                <p class="text-[10px] font-black text-blue-500 uppercase">Ganti Gambar</p>
                            </div>
                        </template>

                        {{-- Tambahkan x-ref="imageInput" di sini --}}
                        <input type="file" name="image" x-ref="imageInput" class="absolute inset-0 opacity-0 cursor-pointer"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { imagePreviewCategory = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            ">
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                    <ul class="list-disc list-inside text-sm text-red-600 font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-2 gap-4 mt-10">
                {{-- Tambahkan logic reset input file di tombol Batal --}}
                <button type="button" 
                    @click="
                        openAddCategory = false; 
                        imagePreviewCategory = null; 
                        $refs.imageInput.value = ''; 
                    " 
                    class="py-4 border-2 border-blue-500 text-blue-500 rounded-2xl font-black uppercase hover:bg-blue-50 transition-colors text-sm">
                    Batal
                </button>
                <button type="submit" class="py-4 bg-[#3b82f6] text-white rounded-2xl font-black uppercase shadow-lg shadow-blue-100 hover:bg-blue-600 transition-colors text-sm">Simpan</button>
            </div>
        </form>
    </div>
    <div x-show="openAddSub" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <form action="{{ route('sub-categories.store') }}" method="POST"
            class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl transition-all">
            @csrf
            <h2 class="text-3xl font-black text-[#1e3a8a] text-center mb-8 uppercase tracking-tight">Sub-Kategori</h2>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Kategori Utama</label>
                    <select name="category_id" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold appearance-none">
                        <option value="">Pilih Kategori Utama</option>
                        @foreach($groupedCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Nama Sub-Kategori</label>
                    <input type="text" name="name" required placeholder="Misal: Ramen, Coffee, Sushi" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-10">
                <button type="button" @click="openAddSub = false" class="py-4 border-2 border-blue-500 text-blue-500 rounded-2xl font-black uppercase hover:bg-blue-50 transition-colors text-sm">Batal</button>
                <button type="submit" class="py-4 bg-[#3b82f6] text-white rounded-2xl font-black uppercase shadow-lg shadow-blue-100 hover:bg-blue-600 transition-colors text-sm">Simpan</button>
            </div>
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                    <ul class="list-disc list-inside text-sm text-red-600 font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <form @submit.prevent="submitUpdate($event)" 
            class="bg-white w-full max-w-lg rounded-[3rem] p-10 shadow-2xl transition-all"
            enctype="multipart/form-data">
            
            <h2 class="text-3xl font-black text-blue-600 mb-6 text-center">UPDATE MENU</h2>

            <div class="space-y-4">
                <input type="text" name="name" x-model="editData.name" placeholder="Nama Menu" 
                    class="w-full px-6 py-4 rounded-2xl bg-gray-100 border-none focus:ring-2 focus:ring-blue-500">

                <div class="flex gap-4">
                    <select name="sub_category_id" x-model="editData.sub_category_id" 
                            class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 border-none">
                        @foreach($groupedCategories as $category)
                            <optgroup label="{{ $category->name }}">
                                @foreach($category->subCategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    <input type="number" name="price" x-model="editData.price" placeholder="Harga" 
                        class="w-80 px-6 py-4 rounded-2xl bg-gray-100 border-none">
                </div>

                <textarea name="desc" x-model="editData.desc" placeholder="Deskripsi menu..." 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-100 border-none h-32"></textarea>

                <div class="flex items-center gap-4">
                    <img :src="editData.image_url" class="w-20 h-20 rounded-2xl object-cover bg-gray-200">
                    <input type="file" name="image" @change="handleEditFile($event)" 
                        class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                        <ul class="list-disc list-inside text-sm text-red-600 font-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" @click="openEdit = false" class="flex-1 py-4 font-bold text-gray-400">BATAL</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-blue-200">SIMPAN PERUBAHAN</button>
            </div>
        </form>
    </div>
    <div x-show="openDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.away="openDetail = false" class="bg-white w-full max-w-md rounded-[3rem] overflow-hidden shadow-2xl relative">
            
            <button @click="openDetail = false" class="absolute top-6 right-6 z-[110] bg-white/80 backdrop-blur-md w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 shadow-sm transition-colors">
                <i class="fas fa-times"></i>
            </button>

            <div class="h-72 bg-gray-100 flex items-center justify-center relative overflow-hidden">
                <template x-if="selectedMenu.image_url">
                    <img :src="selectedMenu.image_url" class="w-full h-full object-cover shadow-inner">
                </template>
                
                <template x-if="!selectedMenu.image_url">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-bowl-rice text-8xl text-gray-200"></i>
                        <span class="text-gray-300 font-bold mt-2">No Image</span>
                    </div>
                </template>
                
                <template x-if="selectedMenu.status !== 'available'">
                    <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center">
                        <div class="bg-red-600 text-white px-8 py-2 rounded-full font-black tracking-[0.2em] uppercase shadow-2xl transform -rotate-3 border-2 border-white">
                            HABIS
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h2 class="text-3xl font-black text-[#1e3a8a] leading-tight" x-text="selectedMenu.name"></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-blue-500 font-bold text-xs bg-blue-50 px-3 py-1 rounded-full uppercase tracking-widest" 
                                x-text="selectedMenu.sub_category ? selectedMenu.sub_category.name : 'Menu'">
                            </span>

                            <span :class="selectedMenu.status === 'available' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'" 
                                class="font-black text-[10px] px-3 py-1 rounded-full uppercase tracking-tighter"
                                x-text="selectedMenu.status === 'available' ? 'Available' : 'Sold Out'">
                            </span>

                            <template x-if="selectedMenu.count_sold > 0">
                                <div class="flex items-center gap-1 bg-amber-100 text-amber-600 font-black text-[10px] px-3 py-1 rounded-full uppercase tracking-tighter border border-amber-200">
                                    <i class="fas fa-crown text-[8px]"></i>
                                    <span x-text="'Terjual ' + selectedMenu.count_sold"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <p class="text-2xl font-black text-blue-600" 
                    x-text="selectedMenu.price ? 'Rp' + parseInt(selectedMenu.price).toLocaleString('id-ID') : 'Rp0'">
                    </p>
                </div>

                <p class="text-gray-500 leading-relaxed mb-8 font-medium italic" x-text="selectedMenu.desc || 'No description available for this delicious menu.'"></p>

                <div class="space-y-3">
                    <button x-show="selectedMenu.status === 'available'"
                            @click="openDetail = false; /* Logic keranjang */" 
                            class="w-full py-5 bg-blue-600 text-white rounded-[2rem] font-black text-xl shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase">
                        <i class="fas fa-cart-plus"></i> Tambah Pesanan
                    </button>

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="openEditMenu(selectedMenu)"
                                class="bg-amber-400 hover:bg-amber-500 text-white py-4 rounded-2xl font-black text-xs transition-all shadow-lg flex items-center justify-center gap-2 uppercase">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                        <button @click="confirmDelete(selectedMenu.id)"  
                                class="bg-red-500 hover:bg-red-600 text-white py-4 rounded-2xl font-black text-xs transition-all shadow-lg flex items-center justify-center gap-2 uppercase">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </div>

                    <button @click="toggleStatus(selectedMenu.id)" 
                            :class="selectedMenu.status === 'available' ? 'border-2 border-red-500 text-red-500' : 'bg-green-500 text-white'"
                            class="w-full py-4 rounded-2xl font-black text-xs flex items-center justify-center gap-2 transition-all uppercase tracking-widest">
                        <i class="fa-solid" :class="selectedMenu.status === 'available' ? 'fa-ban' : 'fa-check-circle'"></i>
                        <span x-text="selectedMenu.status === 'available' ? 'Tandai Stok Habis' : 'Aktifkan Menu'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div x-show="openEditCategory" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl relative transition-all">
            
            {{-- TOMBOL CLOSE (X) DI SUDUT ATAS --}}
            <button @click="openEditCategory = false; imagePreviewCategory = null;" 
                class="absolute top-8 right-8 text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>

            <form :action="'/categories/' + editCategoryData.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <h2 class="text-3xl font-black text-[#1e3a8a] text-center mb-8 uppercase tracking-tight">Edit Kategori</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Nama Kategori</label>
                        <input type="text" name="name" x-model="editCategoryData.name" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Icon Kategori</label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-3xl p-6 flex flex-col items-center justify-center text-gray-400 hover:bg-gray-50 cursor-pointer transition-colors group min-h-[160px]">
                            
                            <template x-if="imagePreviewCategory">
                                <div class="relative w-full h-full flex flex-col items-center">
                                    <img :src="imagePreviewCategory" class="w-24 h-24 object-contain rounded-xl mb-2 shadow-md">
                                    <p class="text-[10px] font-black text-blue-500 uppercase italic">Ganti Icon</p>
                                </div>
                            </template>

                            <input type="file" name="image" x-ref="editImageInput" class="absolute inset-0 opacity-0 cursor-pointer"    
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { imagePreviewCategory = e.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                ">
                                @if ($errors->any())
                                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                                        <ul class="list-disc list-inside text-sm text-red-600 font-bold">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-10">
                    {{-- TOMBOL HAPUS (Memicu Form Hapus Terpisah) --}}
                    <button type="button" 
                        @click="
                            if(confirm('Peringatan Keras: Hapus kategori ini?')) {
                                if(confirm('Data Sub-Kategori dan SEMUA MENU di dalamnya akan ikut terhapus selamanya. Anda yakin?')) {
                                    document.getElementById('delete-category-form').submit();
                                }
                            }
                        " 
                        class="py-4 border-2 border-red-500 text-red-500 rounded-2xl font-black uppercase hover:bg-red-50 transition-colors text-sm">
                        Hapus
                    </button>
                    
                    <button type="submit" class="py-4 bg-[#3b82f6] text-white rounded-2xl font-black uppercase shadow-lg hover:bg-blue-600 transition-colors text-sm">
                        Simpan
                    </button>
                </div>
            </form>

            {{-- FORM HAPUS TERPISAH (HIDDEN) --}}
            <form id="delete-category-form" :action="'/categories/' + editCategoryData.id" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    <div x-show="openEditSub" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl relative transition-all">
            
            {{-- TOMBOL CLOSE (X) --}}
            <button @click="openEditSub = false" class="absolute top-8 right-8 text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>

            <form :action="'/sub-categories/' + editSubData.id" method="POST">
                @csrf
                @method('PUT')
                
                <h2 class="text-3xl font-black text-[#1e3a8a] text-center mb-8 uppercase tracking-tight">Edit Sub Kategori</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase tracking-wide">Nama Sub Kategori</label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="editSubData.name" 
                            required 
                            placeholder="Nama Sub Kategori"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100 font-semibold text-gray-700 shadow-sm transition-all"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 ml-2 uppercase">Induk Kategori</label>
                        <select name="category_id" x-model="editSubData.category_id" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none font-semibold text-gray-700">
                            {{-- UBAH $categories MENJADI $groupedCategories --}}
                            @foreach($groupedCategories as $cat)
                                <option value="{{ $cat->id }}" :selected="editSubData.category_id == {{ $cat->id }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-10">
                    {{-- TOMBOL HAPUS DENGAN 2X KONFIRMASI --}}
                    <button type="button" 
                        @click="
                            if(confirm('Peringatan 1: Anda yakin ingin menghapus sub-kategori ini?')) {
                                if(confirm('Peringatan 2: SEMUA MENU di dalam sub-kategori ini akan terhapus PERMANEN. Lanjutkan?')) {
                                    document.getElementById('delete-sub-form').submit();
                                }
                            }
                        " 
                        class="py-4 border-2 border-red-500 text-red-500 rounded-2xl font-black uppercase hover:bg-red-50 transition-colors text-sm">
                        Hapus
                    </button>
                    
                    <button type="submit" class="py-4 bg-[#3b82f6] text-white rounded-2xl font-black uppercase shadow-lg hover:bg-blue-600 transition-colors text-sm">
                        Update
                    </button>
                </div>
            </form>

            {{-- FORM HAPUS (HIDDEN) --}}
            <form id="delete-sub-form" :action="'/sub-categories/' + editSubData.id" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    {{-- End Pop up Section --}}
    
    <div class="flex h-screen flex-col overflow-hidden">
        
        <header class="bg-white border-b px-6 py-2 flex items-center justify-between h-20 shadow-sm z-10">
            <div class="flex items-center gap-3 w-48">
                <img src="{{ asset('image/Logo.png') }}" alt="Logo" class="h-12">
                <h1 class="text-3xl font-bold text-[#4a90e2]">Kaisei</h1>
            </div>

            <div class="flex-1 flex justify-start ml-10 gap-4">
                <div class="flex flex-col items-center justify-center w-28 h-16 bg-[#3EA1DC] text-white rounded-2xl shadow-md cursor-pointer transition-transform active:scale-95">
                    <i class="fas fa-th-large text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">Dashboard</span>
                </div>
                <div class="flex flex-col items-center justify-center w-28 h-16 bg-white text-gray-400 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50">
                    <i class="fas fa-receipt text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">Bill</span>
                </div>
                <div class="flex flex-col items-center justify-center w-28 h-16 bg-white text-gray-400 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50">
                    <i class="fas fa-user text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">User</span>
                </div>
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            {{-- Sidebar Categories --}}
            <aside class="w-64 bg-white border-r flex flex-col p-5 overflow-y-auto">
                @foreach($groupedCategories as $category)
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xs font-black text-[#1e3a8a] uppercase flex items-center gap-2 cursor-pointer" @click="document.getElementById('{{ $category->name }}').scrollIntoView({ behavior: 'smooth' })">
                                <img src="{{ asset($category->image) }}" alt="img" class="w-5 h-5 object-contain">
                                {{ $category->name }}
                            </h3>
                            <button @click="
                                editCategoryData = {{ json_encode($category) }}; 
                                openEditCategory = true; 
                                imagePreviewCategory = editCategoryData.image ? '/' + editCategoryData.image : null;
                            " class="text-gray-300 hover:text-blue-500 transition-colors">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </div>

                        <ul class="space-y-1 text-sm font-semibold text-gray-500">
                            @foreach($category->subCategories as $sub)
                                <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer transition-all hover:text-[#3b82f6]" 
                                    @click="document.getElementById('{{ Str::slug($sub->name) }}').scrollIntoView({ behavior: 'smooth' })">
                                    <span>{{ $sub->name }}</span>

                                    {{-- Button Edit: Muncul hanya saat Hover --}}
                                    <button 
                                        @click.stop="
                                            editSubData = {{ json_encode($sub) }}; 
                                            openEditSub = true;
                                        "
                                        class="opacity-0 group-hover:opacity-100 text-gray-300 hover:text-blue-500 transition-all duration-200 p-1"
                                    >
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Tombol tambah sub-kategori spesifik per kategori utama --}}
                        <button @click="openAddSub = true" class="mt-4 w-full py-2.5 border-2 border-gray-100 rounded-xl text-[11px] font-bold text-gray-400 hover:bg-gray-50 uppercase tracking-wider transition-all">
                            <i class="fas fa-plus mr-1"></i> Sub-Kategori
                        </button>
                    </div>
                @endforeach

                <button @click="openAddCategory = true" class="mt-auto w-full py-4 bg-[#3b82f6] text-white rounded-2xl font-bold uppercase shadow-lg shadow-blue-100 hover:bg-blue-600 transition-all active:scale-95">
                    <i class="fas fa-plus mr-2"></i> Kategori
                </button>
            </aside>

            {{-- Main --}}
            <main class="flex-1 bg-[#fcfdfe] p-8 overflow-y-auto">
                <div class="mb-10">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <i class="fas fa-search text-gray-300 text-lg"></i>
                        </span>
                        {{-- Tambahkan x-model di sini --}}
                        <input type="text" 
                            x-model="search" 
                            class="w-full pl-14 pr-6 py-4 bg-white border border-gray-100 shadow-sm rounded-3xl focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all text-gray-600 font-semibold" 
                            placeholder="Cari menu favoritmu...">
                    </div>
                </div>
                
                <div class="mb-8">
                    <div @click="openAdd = true" class="w-full border-2 border-dashed border-gray-200 rounded-[2.5rem] py-10 flex items-center justify-center cursor-pointer hover:bg-gray-50 hover:border-blue-200 transition-all text-gray-300 group">
                        <i class="fas fa-plus text-5xl group-hover:scale-110 transition-transform"></i>
                    </div>
                </div>
                @foreach($groupedCategories as $mainCategory)
                    <div class="mt-10">
                        <h1 class="text-4xl font-black text-[#1e3a8a] mb-6 uppercase border-b-4 border-blue-500 inline-block" id={{ $mainCategory->name }}>
                            {{ $mainCategory->name }}
                        </h1>

                        @foreach($mainCategory->subCategories as $sub)
                            @if($sub->menus->count() > 0)
                                {{-- CARI NILAI COUNT_SOLD TERTINGGI DI SUB KATEGORI INI --}}
                                @php
                                    $maxSold = $sub->menus->max('count_sold');
                                @endphp

                                <div class="mt-8 mb-4 flex items-center gap-3" id="{{ Str::slug($sub->name) }}">
                                    <h2 class="text-2xl font-bold text-gray-700 uppercase">{{ $sub->name }}</h2>
                                    <div class="h-[2px] flex-1 bg-gray-100"></div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                                    @foreach($sub->menus as $menu)
                                        @php 
                                            $menuData = $menu->toArray();
                                            $menuData['sub_category'] = $sub; 
                                        @endphp
                                     
                                    
                                        <div x-show="'{{ strtolower($menu->name) }}'.includes(search.toLowerCase())"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        @click="selectedMenu = {{ json_encode($menuData) }}; openDetail = true;" 
                                        class="bg-white p-6 rounded-[2.5rem] shadow-sm hover:shadow-xl transition-all cursor-pointer relative overflow-hidden group">
                                              
                                            {{-- BADGE BEST SELLER OTOMATIS --}}
                                            {{-- Muncul jika count_sold menu ini sama dengan nilai tertinggi DAN bukan 0 --}}
                                            @if($menu->count_sold > 0 && $menu->count_sold == $maxSold)
                                                <div class="absolute top-5 left-0 z-10">
                                                    <div class="bg-amber-400 text-[#1e3a8a] text-[10px] font-black px-3 py-1 rounded-r-full shadow-md flex items-center gap-1 uppercase tracking-tighter">
                                                        <i class="fas fa-fire text-[8px]"></i>
                                                        Best Seller
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- GAMBAR --}}
                                            <div class="w-32 h-32 mx-auto mb-5 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden relative">
                                                @if($menu->image_url)
                                                    <img src="{{ $menu->image_url }}" 
                                                        class="w-full h-full object-cover transition-all duration-300"
                                                        :class="'{{ $menu->status }}' !== 'available' ? 'grayscale opacity-40' : 'group-hover:scale-110'">
                                                @else
                                                    <i class="fas fa-utensils text-4xl text-gray-300"></i>
                                                @endif

                                                @if($menu->status !== 'available')
                                                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                                        <span class="bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded shadow-lg uppercase -rotate-12 border border-white">
                                                            Sold Out
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- TEXT SECTION --}}
                                            <div class="text-center">
                                                <h3 class="font-black text-lg text-[#1e3a8a] leading-tight">{{ $menu->name }}</h3>
                                                <p class="text-sm font-bold text-gray-400 mt-2">Rp. {{ number_format($menu->price, 0, ',', '.') }}</p>
                                                
                                                @if($menu->status !== 'available')
                                                    <div class="mt-2">
                                                        <span class="text-[8px] font-black text-red-500 bg-red-50 px-3 py-1 rounded-full uppercase tracking-tighter">
                                                            Stok Habis
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                     {{-- Pesan jika pencarian tidak ditemukan --}}
                                    <div x-show="search !== '' && !Array.from($el.parentElement.children).some(el => el.style.display !== 'none' && el.tagName === 'DIV')" 
                                        class="col-span-full py-20 text-center">
                                        <div class="bg-gray-50 inline-block p-8 rounded-[3rem]">
                                            <i class="fas fa-search-minus text-4xl text-gray-200 mb-4"></i>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Menu tidak ditemukan</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </main>

            {{-- Sidebar Order Bemu --}}
            <aside class="w-[420px] bg-white border-l flex flex-col h-full shadow-2xl z-10">
                <div class="p-7 flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-14 h-14 bg-[#3b82f6] rounded-[1.2rem] flex items-center justify-center shadow-lg shadow-blue-100">
                            <i class="fas fa-clipboard-list text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-[#1e3a8a]">Order Menu</h2>
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Order No. 164</span>
                        </div>
                    </div>
                </div>
                <div class="mt-auto p-8">
                    <div class="bg-[#2d8aff] rounded-[2.5rem] p-6 flex justify-between items-center">
                        <div class="text-white">
                            <p class="text-xs font-bold uppercase opacity-70 tracking-tighter">Total Items (1)</p>
                            <p class="text-3xl font-black">Rp. 15.000</p>
                        </div>
                        <button class="bg-white text-[#2d8aff] px-10 py-4 rounded-[1.8rem] font-black text-xl">Order</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
   <script type="module">
        function startListening() {
            if (window.Echo) {
                window.Echo.channel('pos-data-channel')
                    .listen('.data.changed', (e) => {
                        window.location.reload();
                    });
            } else {
                console.log('Waiting for Echo...');
                setTimeout(startListening, 500); // Cek lagi setiap 0.5 detik
            }
        }

        startListening();
    </script>
</body>
</html>