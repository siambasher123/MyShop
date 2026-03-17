@extends('layouts.app')
@section('content')

@php
  use Illuminate\Support\Str;
@endphp

<!-- ✅ If your layout already has navbar, do NOT include it again -->

<div class="max-w-7xl mx-auto px-6 py-12 flex flex-col lg:flex-row gap-8">
  
  <!-- 🧭 SIDEBAR FILTER -->
  <aside class="lg:w-1/4 bg-white rounded-2xl shadow-lg p-6 h-fit sticky top-24">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Filters</h2>
    <form method="GET" action="">
      
      <!-- Price Range -->
      <div class="mb-6">
        <label class="block font-medium text-gray-700 mb-2">Price Range ($)</label>
        <div class="flex space-x-3">
          <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                 class="w-1/2 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
          <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                 class="w-1/2 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
      </div>

      <!-- Stock -->
      <div class="mb-6">
        <label class="flex items-center space-x-2">
          <input type="checkbox" name="stock" value="1" {{ request('stock') ? 'checked' : '' }}
                 class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
          <span class="text-gray-700">In Stock Only</span>
        </label>
      </div>

      <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800 transition">
        Apply Filters
      </button>
    </form>
  </aside>

  <!-- 🛍 MAIN PRODUCTS GRID -->
  <section class="flex-1">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
      {{ $category }} › {{ $subcategory }}
    </h1>

    @if($products->isEmpty())
      <div class="text-center text-gray-500 py-20">
        <p>No products found in this subcategory.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @foreach($products as $p)
        <a href="{{ route('shop.product', $p->id) }}" 
           class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition">
          <div class="relative">

            {{-- ✅ Handle both online and local images --}}
            @if(Str::startsWith($p->image, 'http'))
              <img src="{{ $p->image }}" 
                   alt="{{ $p->subcategory }}" 
                   class="w-full h-64 object-cover">
            @else
              <img src="{{ asset('storage/' . $p->image) }}" 
                   alt="{{ $p->subcategory }}" 
                   class="w-full h-64 object-cover">
            @endif

            <span class="absolute top-2 left-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full">
              {{ $p->category }}
            </span>
          </div>
          <div class="p-4">
            <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $p->subcategory }}</h2>
            <p class="text-gray-600 text-sm mt-1 truncate">{{ Str::limit($p->description, 60) }}</p>
            <div class="mt-3 flex items-center justify-between">
              <span class="text-indigo-600 font-bold">${{ number_format($p->price, 2) }}</span>
              @if($p->stock > 0)
                <span class="text-green-600 text-sm font-medium">In Stock</span>
              @else
                <span class="text-red-500 text-sm font-medium">Out of Stock</span>
              @endif
            </div>
          </div>
        </a>
        @endforeach
      </div>
    @endif
  </section>

</div>

<footer class="bg-[#0d0d0d] text-gray-300 py-10 mt-16 text-center">
  <p class="text-sm">&copy; {{ date('Y') }} MyShop. All rights reserved.</p>
</footer>

@endsection
