@extends('layouts.app')

@section('content')

@php
  use Illuminate\Support\Str;
@endphp

<div class="container mx-auto px-6 py-10 flex gap-8">

  {{-- Sidebar Filter --}}
  <aside class="w-1/4 bg-white p-5 rounded-xl shadow-lg h-fit">
    <h3 class="font-bold text-lg mb-4">Filter</h3>

    <form method="GET" action="">
      <label class="block mb-2 font-medium text-gray-700">Price Range</label>
      <div class="flex gap-2 mb-4">
        <input type="number" name="min_price" class="w-1/2 border rounded-lg p-2" placeholder="Min" value="{{ request('min_price') }}">
        <input type="number" name="max_price" class="w-1/2 border rounded-lg p-2" placeholder="Max" value="{{ request('max_price') }}">
      </div>

      <label class="flex items-center mb-4">
        <input type="checkbox" name="stock" value="1" {{ request('stock') ? 'checked' : '' }} class="mr-2">
        Only show in-stock
      </label>

      <button type="submit" class="bg-black text-white w-full py-2 rounded-lg hover:bg-gray-800">
        Apply Filters
      </button>
    </form>
  </aside>

  {{-- Products Grid --}}
  <main class="flex-1">
    <h1 class="text-3xl font-extrabold mb-6 text-gray-900">🧥 {{ $category }} Collection</h1>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      @foreach($products as $product)
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-4">
          <a href="{{ route('shop.product', $product->id) }}">
            
            {{-- ✅ Handle local & external images --}}
            @if(Str::startsWith($product->image, 'http'))
              <img src="{{ $product->image }}" 
                   alt="{{ $product->subcategory }}" 
                   class="w-full h-64 object-cover rounded-lg mb-3">
            @else
              <img src="{{ asset('storage/' . $product->image) }}" 
                   alt="{{ $product->subcategory }}" 
                   class="w-full h-64 object-cover rounded-lg mb-3">
            @endif

            <h2 class="font-semibold text-lg">{{ $product->subcategory }}</h2>
          </a>
          <p class="text-indigo-600 font-bold mt-1">${{ number_format($product->price, 2) }}</p>
        </div>
      @endforeach
    </div>
  </main>
</div>
@endsection
