@extends('template')

@if (Auth::check() && Auth::user()->role_id == 1)
 @section('nav-position', 'flex-1 ml-10')    
 @section('navigation-items')
     <div class="flex gap-2">
         <div class="flex flex-col items-center justify-center w-24 h-14 bg-[#3EA1DC] text-white rounded-xl shadow-sm cursor-pointer">
             <i class="fas fa-th-large text-xl"></i>
             <span class="text-[10px] font-bold mt-1 uppercase">Dashboard</span>
         </div>
         <div class="flex flex-col items-center justify-center w-24 h-14 bg-white text-gray-800 border rounded-xl cursor-pointer">
             <i class="fas fa-receipt text-xl"></i>
             <span class="text-[10px] font-bold mt-1 uppercase">Bill</span>
         </div>
         <div class="flex flex-col items-center justify-center w-24 h-14 bg-white text-gray-800 border rounded-xl cursor-pointer">
             <i class="fas fa-user text-xl"></i>
             <span class="text-[10px] font-bold mt-1 uppercase">User</span>
         </div>
     </div>
@endsection
    
@endif
@section('content')
    @foreach($products as $product)
    <div class="bg-white p-4 rounded-2xl shadow-sm text-center relative">
        <img src="{{ asset('storage/'.$product->image) }}" class="w-24 h-24 mx-auto mb-3 rounded-full object-cover">
        <h3 class="font-bold text-blue-900">{{ $product->name }}</h3>
        <p class="text-xs text-gray-500">Rp. {{ number_format($product->price, 0, ',', '.') }}</p>
    </div>
    @endforeach
    @if (Auth::User()->role_id == 1)
        <div class="border-2 border-dashed border-gray-300 rounded-2xl flex items-center justify-center cursor-pointer hover:bg-gray-100">
            <i class="fas fa-plus text-4xl text-gray-400"></i>
        </div>
    @endif
@endsection

@section('order-items')
    <div class="bg-[#f3f4f6] rounded-2xl p-4 flex items-center gap-4 border border-transparent hover:border-blue-100 transition-all">
        <div class="w-16 h-16 bg-white rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
            <img src="/path-to-shoyu.png" class="w-full h-full object-cover">
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-[#1e3a8a] text-sm">Shoyu Ramen</h4>
            <p class="text-xs text-gray-400 font-semibold">Rp. 15.000</p>
        </div>
        <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-gray-50">
            <button class="text-blue-400 font-bold text-sm">-</button>
            <span class="text-sm font-bold text-gray-800">1</span>
            <button class="text-blue-400 font-bold text-sm">+</button>
        </div>
    </div>
@endsection