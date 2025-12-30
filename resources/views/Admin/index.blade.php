<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaisei POS - Full Layout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom scrollbar agar lebih rapi */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#f0f4f8] font-sans text-gray-800">

    <div class="flex h-screen flex-col overflow-hidden">
        
        <header class="bg-white border-b px-6 py-2 flex items-center justify-between h-20 shadow-sm z-10">
            <div class="flex items-center gap-3 w-56">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-torii-gate text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-[#4a90e2] tracking-tight">Kaisei</h1>
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
            
            <aside class="w-64 bg-white border-r flex flex-col p-5 overflow-y-auto">
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xs font-black text-[#1e3a8a] uppercase flex items-center gap-2">
                            <i class="fas fa-utensils"></i> Makanan
                        </h3>
                        <button class="text-gray-300 hover:text-blue-500"><i class="fas fa-edit text-xs"></i></button>
                    </div>
                    <ul class="space-y-1 text-sm font-semibold text-gray-500">
                        <li class="bg-[#e2f0ff] text-[#3b82f6] p-3 rounded-xl flex justify-between items-center group">
                             Ramen 
                        </li>
                        <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer">
                            Rice Bowl 
                        </li>
                        <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer">
                            Sushi 
                        </li>
                        <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer text-gray-400">
                            Snack & Dessert
                        </li>
                    </ul>
                    <button class="mt-4 w-full py-2.5 border-2 border-gray-100 rounded-xl text-[11px] font-bold text-gray-400 hover:bg-gray-50 uppercase tracking-wider">
                        <i class="fas fa-plus mr-1"></i> Sub-Kategori
                    </button>
                </div>

                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xs font-black text-[#1e3a8a] uppercase flex items-center gap-2">
                            <i class="fas fa-coffee"></i> Minuman
                        </h3>
                        <button class="text-gray-300 hover:text-blue-500"><i class="fas fa-edit text-xs"></i></button>
                    </div>
                    <ul class="space-y-1 text-sm font-semibold text-gray-500">
                        <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer">Tea</li>
                        <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer">Coffee</li>
                        <li class="p-3 hover:bg-gray-50 rounded-xl flex justify-between items-center group cursor-pointer">Milk</li>
                    </ul>
                    <button class="mt-4 w-full py-2.5 border-2 border-gray-100 rounded-xl text-[11px] font-bold text-gray-400 hover:bg-gray-50 uppercase tracking-wider">
                        <i class="fas fa-plus mr-1"></i> Sub-Kategori
                    </button>
                </div>

                <button class="mt-auto w-full py-4 bg-[#3b82f6] text-white rounded-2xl font-bold uppercase shadow-lg shadow-blue-100 hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Kategori
                </button>
            </aside>

            <main class="flex-1 bg-[#fcfdfe] p-8 overflow-y-auto">
                <div class="mb-10">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <i class="fas fa-search text-gray-300 text-lg"></i>
                        </span>
                        <input type="text" class="w-full pl-14 pr-6 py-4 bg-white border border-gray-100 shadow-sm rounded-3xl focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all text-gray-600" placeholder="Search">
                    </div>
                </div>
                
                <div class="mb-8">
                    <div class="w-full border-2 border-dashed border-gray-200 rounded-[2.5rem] py-10 flex items-center justify-center cursor-pointer hover:bg-gray-50 hover:border-blue-200 transition-all text-gray-300 group">
                        <i class="fas fa-plus text-5xl group-hover:scale-110 transition-transform"></i>
                    </div>
                </div>

                <h2 class="text-3xl font-black text-[#1e3a8a] mb-8 uppercase tracking-tight">Ramen <i class="fas fa-edit text-xxl opacity-40 ml-3"></i></h2> 

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm hover:shadow-xl transition-all text-center border border-gray-50 group cursor-pointer relative overflow-hidden">
                        <div class="absolute top-4 right-4 bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-plus text-sm"></i>
                        </div>
                        <div class="w-32 h-32 mx-auto mb-5 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden shadow-inner">
                             <i class="fas fa-bowl-rice text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="font-black text-lg text-[#1e3a8a]">Shoyu Ramen</h3>
                        <p class="text-sm font-bold text-gray-400 mt-1 tracking-wide">Rp. 15.000</p>
                    </div>

                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm hover:shadow-xl transition-all text-center border border-gray-50 group cursor-pointer relative">
                        <div class="w-32 h-32 mx-auto mb-5 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden shadow-inner">
                            <i class="fas fa-utensils text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="font-black text-lg text-[#1e3a8a]">Shio Ramen</h3>
                        <p class="text-sm font-bold text-gray-400 mt-1 tracking-wide">Rp. 13.000</p>
                    </div>

                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm hover:shadow-xl transition-all text-center border border-gray-50 group cursor-pointer relative">
                        <div class="w-32 h-32 mx-auto mb-5 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden shadow-inner">
                            <i class="fas fa-bowl-food text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="font-black text-lg text-[#1e3a8a]">Miso Ramen</h3>
                        <p class="text-sm font-bold text-gray-400 mt-1 tracking-wide">Rp. 13.000</p>
                    </div>
                </div>
            </main>

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
                    <div class="flex gap-3 text-gray-300 mt-2">
                        <button class="hover:text-blue-500 transition-colors"><i class="fas fa-edit text-xl"></i></button>
                        <button class="hover:text-blue-500 transition-colors"><i class="fas fa-ellipsis-v text-xl"></i></button>
                    </div>
                </div>

                <hr class="mx-7 border-gray-50">

                <div class="flex-1 overflow-y-auto px-7 py-6 space-y-5">
                    
                    <div class="bg-[#f8fafc] rounded-[2rem] p-4 flex items-center gap-4 border border-transparent hover:border-blue-100 transition-all group">
                        <div class="w-16 h-16 bg-white rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm flex items-center justify-center">
                            <i class="fas fa-bowl-rice text-gray-300"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-[#1e3a8a] text-sm">Shoyu Ramen</h4>
                            <p class="text-xs text-gray-400 font-bold mt-0.5">Rp. 15.000</p>
                        </div>
                        <div class="flex items-center gap-3 bg-white px-3 py-2 rounded-2xl shadow-sm border border-gray-50">
                            <button class="text-blue-400 font-black text-xl leading-none hover:text-blue-600">-</button>
                            <span class="text-sm font-black text-gray-800">1</span>
                            <button class="text-blue-400 font-black text-xl leading-none hover:text-blue-600">+</button>
                        </div>
                    </div>

                    <div class="bg-[#f8fafc] rounded-[2rem] p-4 flex items-center gap-4 border border-transparent hover:border-blue-100 transition-all group">
                        <div class="w-16 h-16 bg-white rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm flex items-center justify-center">
                            <i class="fas fa-bowl-food text-gray-300"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-[#1e3a8a] text-sm">Miso Ramen</h4>
                            <p class="text-xs text-gray-400 font-bold mt-0.5">Rp. 13.000</p>
                        </div>
                        <div class="flex items-center gap-3 bg-white px-3 py-2 rounded-2xl shadow-sm border border-gray-50">
                            <button class="text-blue-400 font-black text-xl leading-none hover:text-blue-600">-</button>
                            <span class="text-sm font-black text-gray-800">1</span>
                            <button class="text-blue-400 font-black text-xl leading-none hover:text-blue-600">+</button>
                        </div>
                    </div>

                </div>

                <div class="p-8">
                    <div class="bg-[#2d8aff] rounded-[2.5rem] p-6 flex justify-between items-center shadow-2xl shadow-blue-200">
                        <div class="text-white">
                            <p class="text-xs font-bold uppercase opacity-70 tracking-tighter">Total Items (2)</p>
                            <p class="text-3xl font-black">Rp. 28.000</p>
                        </div>
                        <button class="bg-white text-[#2d8aff] px-10 py-4 rounded-[1.8rem] font-black text-xl hover:scale-105 transition-transform active:scale-95 shadow-md">
                            Order
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

</body>
</html>