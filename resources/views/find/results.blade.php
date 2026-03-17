@php
  use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10 flex gap-8">

  {{-- 🧭 Sidebar Filter --}}
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

  {{-- 🛍 Product Grid --}}
  <main class="flex-1">
    <h1 class="text-3xl font-extrabold mb-6 text-gray-900">
      🔍 Results for "{{ $query }}"
    </h1>

    @if($results->isEmpty())
      <div class="text-center text-gray-500 py-20">
        <p>No products found matching your search.</p>
      </div>
    @else
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($results as $product)
          <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-4">
            <a href="{{ route('shop.product', $product->id) }}">

              {{-- ✅ Smart Image Fetcher from products1.image --}}
              @php
                $imagePath = $product->image ?? '';
                $finalImage = '';

                if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                    // 🌐 External / online image
                    $finalImage = $imagePath;
                } elseif (Str::startsWith($imagePath, ['storage/', '/storage/'])) {
                    // 🗄 Image already stored in /storage/
                    $finalImage = asset(ltrim($imagePath, '/'));
                } elseif (Str::startsWith($imagePath, ['public/', '/public/'])) {
                    // 🗂 Public folder image → convert to storage URL
                    $finalImage = asset(Str::replaceFirst('public/', 'storage/', $imagePath));
                } elseif (!empty($imagePath)) {
                    // 📦 Just a filename or relative path → assume it's in storage
                    $finalImage = asset('storage/' . ltrim($imagePath, '/'));
                } else {
                    // ❌ No image available → fallback icon
                    $finalImage = 'https://cdn-icons-png.flaticon.com/512/126/126477.png';
                }
              @endphp

              <img src="{{ $finalImage }}"
                   alt="{{ $product->subcategory ?? $product->name }}"
                   class="w-full h-64 object-cover rounded-lg mb-3">

              {{-- 🏷 Product Title --}}
              <h2 class="font-semibold text-lg truncate">
                {{ $product->subcategory ?? $product->name }}
              </h2>
            </a>

            {{-- 💰 Product Price --}}
            <p class="text-indigo-600 font-bold mt-1">
              ${{ number_format($product->price, 2) }}
            </p>
          </div>
        @endforeach
      </div>
    @endif
  </main>
</div>
@endsection
