<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
<body class="bg-[#f0f4f8] font-sans text-gray-800" x-data="{
        openAdd: false,
        openDetail: false,
        selectedMenu: {},
        search: '',
    }">
    
     {{-- Modal Detail Menu --}}
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
                </div>
            </div>
        </div>
    </div>
    <div class="flex h-screen flex-col overflow-hidden">
        
        <header class="bg-white border-b px-6 py-2 flex items-center justify-between h-20 shadow-sm z-10">
             <div class="flex items-center gap-3 w-48">
                <img src="{{ asset('image/Logo.png') }}" alt="Logo" class="h-12">
                <h1 class="text-3xl font-bold text-[#4a90e2]">Kaisei</h1>
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

                        </div>

                        <ul class="space-y-1 text-sm font-semibold text-gray-500">
                            @foreach($category->subCategories as $sub)
                                <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer transition-all hover:text-[#3b82f6]" @click="document.getElementById('{{ Str::slug($sub->name) }}').scrollIntoView({ behavior: 'smooth' })">
                                    {{ $sub->name }}
                                    {{-- Dot indikator kalau mau --}}
                                    <span class="w-1.5 h-1.5 rounded-full bg-transparent group-hover:bg-[#3b82f6]"></span>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Tombol tambah sub-kategori spesifik per kategori utama --}}
                       
                    </div>
                @endforeach

               
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
        // Gunakan pengecekan berkala sampai window.Echo tersedia
        const initEcho = () => {
            if (window.Echo) {
                window.Echo.channel('pos-data-channel')
                    .listen('.data.changed', (e) => {
                        console.log('Update detected:', e.type);
                        window.location.reload();
                    });
            } else {
                // Cek lagi setelah 500ms jika belum siap
                setTimeout(initEcho, 500);
            }
        };

        document.addEventListener('DOMContentLoaded', initEcho);
    </script>
</body>
</html>