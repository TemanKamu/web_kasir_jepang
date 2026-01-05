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
            {{-- Kontent utama --}}
        </div>
    </div>
    
</body>
</html>