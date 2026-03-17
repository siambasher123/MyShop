@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Search results</h1>
    <p class="text-gray-600 mt-1">
      Query: <span class="font-medium">"{{ $q }}"</span>
    </p>
    @if(!empty($filters))
      <div class="mt-3 flex flex-wrap gap-2">
        @foreach(['category','subcategory'] as $key)
          @if(!empty($filters[$key]))
            <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 border">{{ ucfirst($key) }}: {{ $filters[$key] }}</span>
          @endif
        @endforeach
        @if(!is_null($filters['min_price'])) <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 border">Min: ${{ number_format($filters['min_price'],2) }}</span> @endif
        @if(!is_null($filters['max_price'])) <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 border">Max: ${{ number_format($filters['max_price'],2) }}</span> @endif
        @if(!is_null($filters['in_stock']))  <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 border">{{ $filters['in_stock'] ? 'In Stock' : 'Any Stock' }}</span> @endif
        @if(!empty($filters['keywords']))
          @foreach($filters['keywords'] as $kw)
            <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800 border">#{{ $kw }}</span>
          @endforeach
        @endif
      </div>
    @endif
  </div>

  @if($products->isEmpty())
    <div class="bg-white rounded-2xl shadow p-10 text-center text-gray-500">
      No matching products found.
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-7">
      @foreach($products as $p)
        <a href="{{ route('shop.product', $p->id) }}" class="bg-white rounded-2xl shadow hover:shadow-2xl overflow-hidden group transition">
          <div class="relative">
            <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->subcategory }}"
                 class="w-full h-64 object-cover group-hover:scale-[1.02] transition">
            <span class="absolute top-2 left-2 bg-black/80 text-white text-[11px] px-2 py-1 rounded">{{ $p->category }}</span>
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-gray-900 truncate">{{ $p->subcategory }}</h3>
            <p class="text-sm text-gray-500 truncate mt-1">{{ \Illuminate\Support\Str::limit($p->description, 60) }}</p>

            <div class="mt-3 flex items-baseline gap-2">
              @if($p->old_price && $p->old_price > $p->price)
                <span class="text-gray-400 line-through">${{ number_format($p->old_price, 2) }}</span>
              @endif
              <span class="text-[17px] font-bold text-gray-900">${{ number_format($p->price, 2) }}</span>
            </div>

            <div class="mt-2 text-xs {{ $p->stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
              {{ $p->stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </div>
          </div>
        </a>
      @endforeach
    </div>

    <div class="mt-8">
      {{ $products->links() }}
    </div>
  @endif
</div>
@endsection
