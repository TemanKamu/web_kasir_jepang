<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaisei POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#f0f4f8] font-sans">
    <div class="flex h-screen flex-col">
        <header class="bg-white border-b px-6 py-2 flex items-center justify-between h-20 shadow-sm">
            <div class="flex items-center gap-3 w-48">
                <img src="{{ asset('image/Logo.png') }}" alt="Logo" class="h-12">
                <h1 class="text-3xl font-bold text-[#4a90e2]">Kaisei</h1>
            </div>

            <div class="flex-1 flex justify-start ml-10">
                @yield('navigation-items')
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            <aside class="w-56 bg-white border-r flex flex-col p-4 overflow-y-auto">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-black text-[#1e3a8a] uppercase flex items-center gap-2">
                            <i class="fas fa-utensils"></i> Makanan
                        </h3>
                        <button class="text-gray-400 hover:text-blue-500"><i class="fas fa-edit text-xs"></i></button>
                    </div>
                    <ul class="space-y-1 text-sm font-semibold text-gray-500">
                        <li class="bg-[#e2f0ff] text-[#3b82f6] p-2 rounded-lg flex justify-between items-center">
                             Ramen {{-- <i class="fas fa-edit text-xs opacity-50 cursor-pointer"></i> --}} 
                        </li>
                        <li class="p-2 hover:bg-gray-50 rounded-lg flex justify-between items-center">
                            Rice Bowl <i class="fas fa-edit text-xs opacity-0 hover:opacity-100"></i>
                        </li>
                        <li class="p-2 hover:bg-gray-50 rounded-lg flex justify-between items-center">
                            Sushi <i class="fas fa-edit text-xs opacity-0 hover:opacity-100"></i>
                        </li>
                        <li class="p-2 hover:bg-gray-50 rounded-lg flex justify-between items-center">
                            Snack & Dessert <i class="fas fa-edit text-xs opacity-0 hover:opacity-100"></i>
                        </li>
                    </ul>
                    <button class="mt-3 w-full py-2 border-2 border-gray-200 rounded-lg text-xs font-bold text-gray-400 hover:bg-gray-50 uppercase">
                        <i class="fas fa-plus mr-1"></i> Sub-Kategori
                    </button>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xs font-black text-[#1e3a8a] uppercase flex items-center gap-2">
                            <i class="fas fa-coffee"></i> Minuman
                        </h3>
                        <button class="text-gray-400 hover:text-blue-500"><i class="fas fa-edit text-xs"></i></button>
                    </div>
                    <ul class="space-y-1 text-sm font-semibold text-gray-500">
                        <li class="p-2 hover:bg-gray-50 rounded-lg flex justify-between items-center">Tea</i></li>
                        <li class="p-2 hover:bg-gray-50 rounded-lg flex justify-between items-center">Coffee</li>
                        <li class="p-2 hover:bg-gray-50 rounded-lg flex justify-between items-center">Milk</li>
                    </ul>
                    <button class="mt-3 w-full py-2 border-2 border-gray-200 rounded-lg text-xs font-bold text-gray-400 hover:bg-gray-50 uppercase">
                        <i class="fas fa-plus mr-1"></i> Sub-Kategori
                    </button>
                </div>

                <button class="mt-auto w-full py-3 bg-[#3b82f6] text-white rounded-xl font-bold uppercase shadow-md hover:bg-blue-600">
                    <i class="fas fa-plus mr-2"></i> Kategori
                </button>
            </aside>

            <main class="flex-1 bg-[#fcfdfe] p-8 overflow-y-auto">
                <div class="mb-8">
                    <div class="relative max-w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                            <i class="fas fa-search text-gray-300"></i>
                        </span>
                        <input type="text" class="w-full pl-12 pr-4 py-4 bg-white border border-gray-100 shadow-sm rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Search">
                    </div>
                </div>
                
                <div class="w-full">
                    @yield('content')
                </div>
            </main>

            <aside class="w-[400px] bg-white border-l flex flex-col h-full shadow-lg">
                <div class="p-6 flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-[#3b82f6] rounded-2xl flex items-center justify-center shadow-lg shadow-blue-100">
                            <i class="fas fa-clipboard-list text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-[#1e3a8a]">Order Menu</h2>
                            <span class="text-sm text-gray-400 font-bold uppercase tracking-wider">Order No. 164</span>
                        </div>
                    </div>
                    <div class="flex gap-3 text-gray-300 mt-2">
                        <button class="hover:text-blue-500"><i class="fas fa-edit text-lg"></i></button>
                        <button class="hover:text-blue-500"><i class="fas fa-ellipsis-v text-lg"></i></button>
                    </div>
                </div>

                <hr class="mx-6 border-gray-100">

                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-4">
                    @yield('order-items')
                </div>

                <div class="p-6">
                    <div class="bg-[#2d8aff] rounded-[2.5rem] p-6 flex justify-between items-center shadow-xl shadow-blue-200">
                        <div class="text-white">
                            <p class="text-sm font-medium opacity-80">0 items</p>
                            <p class="text-3xl font-black">Rp. 0</p>
                        </div>
                        <button class="bg-white text-[#2d8aff] px-12 py-4 rounded-[1.5rem] font-black text-xl hover:scale-105 transition-transform">
                            Order
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>