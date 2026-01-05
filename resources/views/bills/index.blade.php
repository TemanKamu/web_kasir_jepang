<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaisei POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#f0f4f8] font-sans" 
    x-data="{ 
        showDetail: false, 
        selectedBill: null,
        openDetail(bill) {
            this.selectedBill = bill;
            this.showDetail = true;
        }
    }">
    <div x-show="showDetail" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl overflow-hidden p-8" @click.away="showDetail = false">
            
            <div class="text-center mb-6">
                <h3 class="text-2xl font-black text-gray-800 tracking-tighter">KAISEI POS</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                    No. Antrian: #<span x-text="selectedBill?.queue_number"></span>
                </p>
            </div>

            <div class="space-y-4 mb-6 border-b border-dashed border-gray-200 pb-6">
                <template x-for="item in selectedBill?.ordered_menus" :key="item.id">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-gray-800 text-sm" x-text="item.menu.name"></p>
                            <p class="text-[10px] text-gray-400 font-bold" x-text="item.quantity + ' x Rp ' + new Intl.NumberFormat('id-ID').format(item.menu.price)"></p>
                        </div>
                        <p class="font-bold text-gray-800 text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.total_price)"></p>
                    </div>
                </template>
            </div>

            <div class="space-y-2 mb-8">
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">TOTAL</span>
                    <span class="font-black text-gray-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBill?.amount_paid)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">BAYAR</span>
                    <span class="font-bold text-gray-600 text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBill?.amount_paid + selectedBill?.change)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-black text-green-500 uppercase tracking-wider">KEMBALI</span>
                    <span class="font-black text-green-500 text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBill?.change)"></span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Metode:</span>
                    <span class="font-black text-gray-800 text-xs uppercase" x-text="selectedBill?.payment_method"></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="showDetail = false" class="flex-1 py-4 bg-gray-50 text-gray-400 font-bold rounded-2xl hover:bg-gray-100 transition-all">Tutup</button>
                <button class="flex-1 py-4 bg-[#3EA1DC]/10 text-[#3EA1DC] font-bold rounded-2xl flex items-center justify-center gap-2 hover:bg-[#3EA1DC] hover:text-white transition-all shadow-sm">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>
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
                <a href="{{ route('bills.index') }}" class="flex flex-col items-center justify-center w-28 h-16 bg-[#3EA1DC] text-white rounded-2xl shadow-md cursor-pointer transition-transform active:scale-95">
                    <i class="fas fa-receipt text-xl"></i>
                    <span class="text-[11px] font-bold mt-1 uppercase">Bill</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex flex-col items-center justify-center w-28 h-16  bg-white text-gray-400 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all">
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

        <div class="flex flex-1 overflow-hidden">
            {{-- Kontent utama --}}
            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-8">
                        <h2 class="text-3xl font-black text-[#1e3a8a] tracking-tight">Order History</h2>
                        <p class="text-gray-500 mt-1">Manage billing information and view receipts</p>
                    </div>

                    <form action="{{ route('bills.index') }}" method="GET" class="flex flex-wrap items-center gap-4 mb-6">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Order ID or Queue Number..." 
                                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-400 transition-all shadow-sm text-sm">
                        </div>

                        <div class="relative">
                            <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            <input type="date" name="date" value="{{ request('date') }}"
                                class="pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-400 transition-all shadow-sm text-sm font-bold text-gray-600">
                        </div>

                        <select name="status" onchange="this.form.submit()" 
                            class="px-6 py-3 bg-white border border-gray-100 rounded-2xl font-bold text-gray-600 outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>

                        <select name="sort_price" onchange="this.form.submit()" 
                            class="px-6 py-3 bg-white border border-gray-100 rounded-2xl font-bold text-gray-600 outline-none focus:ring-2 focus:ring-blue-400 shadow-sm text-sm appearance-none cursor-pointer">
                            <option value="">Urutkan Harga</option>
                            <option value="desc" {{ request('sort_price') == 'desc' ? 'selected' : '' }}>Tertinggi</option>
                            <option value="asc" {{ request('sort_price') == 'asc' ? 'selected' : '' }}>Terendah</option>
                        </select>

                        <div class="flex gap-2">
                            <button type="submit" class="px-6 py-3 bg-[#3EA1DC] text-white rounded-2xl font-bold shadow-md hover:bg-blue-500 transition-all text-sm">
                                Filter
                            </button>
                            @if(request()->anyFilled(['search', 'date', 'status', 'sort_price']))
                                <a href="{{ route('bills.index') }}" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition-all text-sm flex items-center justify-center">
                                    <i class="fas fa-redo"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">Order ID / Date</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">Antrean</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Admin</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">Harga</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest">Metode</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Status</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($bills as $bill)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-800">{{ $bill->code }} </div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase">{{ \Carbon\Carbon::parse($bill->date)->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-[#3EA1DC] flex items-center justify-center font-black text-sm">
                                            {{ $bill->queue_number }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
                                            <div class="w-5 h-5 bg-[#3EA1DC] rounded-full flex items-center justify-center text-[8px] text-white">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700">{{ $bill->user->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 font-black text-gray-800">
                                        Rp {{ number_format($bill->amount_paid, 0, ',', '.') }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-bold text-gray-500 uppercase">{{ $bill->payment_method }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider
                                            {{ $bill->status == 'completed' ? 'bg-green-100 text-green-600' : 
                                            ($bill->status == 'pending' ? 'bg-orange-100 text-orange-600' : 
                                            'bg-red-100 text-red-600') }}"> {{ $bill->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openDetail({{ json_encode($bill) }})" title="View Details" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-400 rounded-xl hover:bg-blue-500 hover:text-white transition-all">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                            @if($bill->status !== 'cancelled')
                                                {{-- Tombol Aktif --}}
                                                <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" 
                                                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan bill ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            title="Cancel Bill" 
                                                            class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Tombol Disabled: Muncul jika status sudah 'cancelled' --}}
                                                <div title="Bill has been cancelled" 
                                                    class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-300 rounded-xl cursor-not-allowed border border-gray-200">
                                                    <i class="fas fa-ban text-xs"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex items-center justify-between px-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                Showing {{ $bills->firstItem() }} to {{ $bills->lastItem() }} of {{ $bills->total() }} Results
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                Page {{ $bills->currentPage() }} of {{ $bills->lastPage() }}
                            </span>
                            
                            <div class="flex gap-1">
                                {{-- Tombol Previous --}}
                                @if ($bills->onFirstPage())
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </div>
                                @else
                                    <a href="{{ $bills->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-lg text-gray-400 hover:text-[#3EA1DC] hover:border-[#3EA1DC] transition-all">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </a>
                                @endif

                                {{-- Tombol Next --}}
                                @if ($bills->hasMorePages())
                                    <a href="{{ $bills->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-100 rounded-lg text-gray-400 hover:text-[#3EA1DC] hover:border-[#3EA1DC] transition-all">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                                @else
                                    <div class="w-8 h-8 flex items-center justify-center bg-gray-50 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script type="module">
    function startListening() {
            if (window.Echo) {
                window.Echo.channel('pos-data-channel')
                    .listen('.data.changed', (e) => {
                        console.log('Data changed, reloading...');
                        window.location.reload();
                    });

            } else {
                setTimeout(startListening, 500); 
            }
        }

        startListening();
    </script>
</body>
</html>