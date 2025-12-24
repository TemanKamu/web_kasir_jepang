<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('image/Logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 min-h-screen relative overflow-hidden">

    <div 
        class="fixed inset-0 z-0 opacity-25 bg-cover bg-center bg-no-repeat pointer-events-none"
        style="background-image: url('{{ asset('image/Wave_Background.jpg') }}');"
    ></div>
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-[400px] p-10 flex flex-col items-center scale-125">
            
            <div class="mb-8">
                <img src="{{ asset('image/Logo.png') }}" alt="Logo" class="h-28 w-auto">
            </div>

            <form  action=" {{ route('login') }}" method="POST" class="w-full space-y-4">
                @csrf
                
                <div class="relative flex items-center group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-300 group-focus-within:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="email" name="email" placeholder="EMAIL" 
                        class="w-full pl-10 pr-4 py-2.5 border border-blue-200  rounded-md focus:outline-none focus:ring-1 focus:ring-blue-300 focus:border-blue-300 text-sm text-blue-500 placeholder-blue-300 tracking-widest uppercase transition-all">
                </div>

                <div class="relative flex items-center group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-300 group-focus-within:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" name="password" placeholder="PASSWORD" 
                        class="w-full pl-10 pr-4 py-2.5 border border-blue-200 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-300 focus:border-blue-300 text-sm text-blue-500 placeholder-blue-300 tracking-widest uppercase transition-all">
                </div>
                @if ($errors->any())
                    <ul>
                         @foreach ($errors->all() as $error)
                             <li class="text-sm text-red-500">* {{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="pt-4">
                    <button type="submit" 
                        class="w-full py-3 bg-white border border-blue-100 shadow-md rounded-md text-blue-500 font-bold tracking-[0.2em] hover:bg-blue-50 hover:shadow-lg hover:border-blue-200 transition-all uppercase text-sm">
                        Login
                    </button>
                </div>

            </form>
        </div>
    </div>

</body>
</html>