@extends('layouts.app')

@section('content')

@php
  use Illuminate\Support\Str;
@endphp

<div class="bg-gray-50 min-h-screen py-10 px-4 md:px-10">
  <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10">

    <!-- 🖼️ PRODUCT IMAGE -->
    <div class="flex justify-center items-start">
      <div class="bg-white rounded-xl shadow-md overflow-hidden max-w-md w-full">
        @if(Str::startsWith($product->image, 'http'))
          <img src="{{ $product->image }}" alt="{{ $product->subcategory }}"
               class="w-full h-[520px] object-cover hover:scale-[1.02] transition-transform duration-300">
        @else
          <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->subcategory }}"
               class="w-full h-[520px] object-cover hover:scale-[1.02] transition-transform duration-300">
        @endif
      </div>
    </div>

    <!-- 🧾 PRODUCT DETAILS -->
    <div>
      <h1 class="text-3xl font-semibold text-gray-900 mb-3">{{ $product->subcategory }}</h1>
      <p class="text-sm text-gray-600 flex items-center mb-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
        </svg>
        Ask for details
      </p>

      <p class="text-gray-700 mb-1"><strong>Code:</strong> {{ $product->code }}</p>

      <!-- 💰 PRICE SECTION -->
      <div class="flex items-center space-x-3 mb-6">
        @if($product->old_price && $product->old_price > $product->price)
          <p class="text-lg text-gray-500 line-through decoration-2 decoration-gray-400">
            ${{ number_format($product->old_price, 2) }}
          </p>
          <p class="text-3xl font-extrabold text-gray-900">
            ${{ number_format($product->price, 2) }}
          </p>
          <span class="bg-emerald-50 text-emerald-600 text-sm font-semibold px-3 py-1 rounded-full shadow-sm border border-emerald-100">
            -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
          </span>
        @else
          <p class="text-3xl font-extrabold text-gray-900">${{ number_format($product->price, 2) }}</p>
        @endif
      </div>

      <!-- 🛒 ADD TO CART -->
      <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-8">
        @csrf
        <div class="flex items-center mb-6 space-x-3">
          <label class="text-gray-700 font-medium">Qty:</label>
          <div class="flex items-center border rounded-md overflow-hidden">
            <button type="button" class="decrement px-3 py-2 text-gray-600 hover:bg-gray-200">−</button>
            <input type="number" name="quantity" value="1" min="1" class="quantity w-12 text-center border-x">
            <button type="button" class="increment px-3 py-2 text-gray-600 hover:bg-gray-200">+</button>
          </div>
        </div>

        <button type="submit" class="flex items-center bg-gray-900 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-800 transition-all duration-300 transform hover:scale-[1.02]">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" class="w-6 h-6 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m10-9l2 9m-6-9v9" />
          </svg>
          Add to Cart
        </button>
      </form>

      <!-- ✨ DELIVERY INFO -->
      <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6 shadow-sm mt-8 hover:shadow-md transition">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
          </svg>
          Why You’ll Love This Product
        </h3>
        <ul class="space-y-3 text-[16px] text-gray-700 leading-relaxed">
          <li><span class="text-emerald-600 mr-3 text-lg">✔️</span><b>Fast Delivery:</b> 1–2 days.</li>
          <li><span class="text-blue-500 mr-3 text-lg">💎</span><b>Premium Quality:</b> 100% authentic.</li>
          <li><span class="text-amber-500 mr-3 text-lg">💰</span><b>Cash On Delivery:</b> Pay on delivery!</li>
          <li><span class="text-indigo-500 mr-3 text-lg">🚚</span><b>Delivery:</b> Inside $1.20, Outside $0.60.</li>
        </ul>
      </div>

      <!-- 📞 CONTACT BOX -->
      <div class="bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 rounded-xl p-6 shadow-sm mt-8 hover:shadow-md transition">
        <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618V15.38a1 1 0 01-1.447.894L15 14M4 6h5a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
          </svg>
          Need Help? Contact Us Anytime
        </h3>

        <ul class="space-y-3 text-[16px] font-medium text-gray-800">
          <li><span class="text-emerald-600 mr-2 text-lg">📞</span> 09617060097</li>
          <li><span class="text-emerald-600 mr-2 text-lg">📞</span> 01306639244 — <b>Bkash Personal</b></li>
          <li><span class="text-emerald-600 mr-2 text-lg">📞</span> 01306639244 — <b>Nagad Personal</b></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 💬 TABS -->
  <div class="max-w-7xl mx-auto mt-12">
    <div class="flex border-b">
      <button class="tab-btn px-5 py-3 font-semibold text-gray-800 border-b-2 border-gray-900" data-tab="desc">DESCRIPTION</button>
      <button class="tab-btn px-5 py-3 font-semibold text-gray-600 hover:text-gray-900" data-tab="order">HOW TO ORDER</button>
      <button class="tab-btn px-5 py-3 font-semibold text-gray-600 hover:text-gray-900" data-tab="review">REVIEWS</button>
    </div>

    <div id="desc" class="tab-content bg-white p-8 rounded-b-lg shadow mt-2">
      <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
    </div>

    <div id="order" class="tab-content bg-white p-8 rounded-b-lg shadow mt-2 hidden">
      <ul class="list-disc pl-6 space-y-2 text-gray-700">
        <li>Select your quantity</li>
        <li>Click <b>Add To Cart</b></li>
        <li>Proceed to checkout</li>
        <li>Sign up (if new)</li>
        <li>Complete checkout — we’ll confirm and deliver!</li>
      </ul>
    </div>

    <!-- ⭐ Reviews -->
    <div id="review" class="tab-content bg-white p-8 rounded-b-lg shadow mt-2 hidden">
      <h4 class="text-2xl font-bold mb-4 text-gray-900">Reviews</h4>
      <p class="text-gray-600">⭐ 0 Reviews</p>

      <div class="mt-6">
        <button id="toggleReviewBox" class="border border-gray-400 text-gray-800 px-5 py-2 rounded-lg hover:bg-gray-100">
          ✏️ Write a Review
        </button>
      </div>

      <!-- Inline Review Box -->
      <div id="reviewBox" class="hidden mt-6 bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-sm transition-all duration-300 ease-in-out">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Write a Review</h3>
        <form action="{{ route('review.store') }}" method="POST" class="space-y-4">
          @csrf
          <input type="hidden" name="category" value="{{ $product->category }}">
          <input type="hidden" name="subcategory" value="{{ $product->subcategory }}">
          <input type="hidden" name="code" value="{{ $product->code }}">
          <input type="hidden" name="price" value="{{ $product->price }}">

          <div>
            <label class="block text-gray-700 font-medium mb-1">Rating</label>
            <select name="rating" required class="w-full border rounded-lg p-2">
              <option value="">Select Rating</option>
              <option value="5">⭐⭐⭐⭐⭐</option>
              <option value="4">⭐⭐⭐⭐</option>
              <option value="3">⭐⭐⭐</option>
              <option value="2">⭐⭐</option>
              <option value="1">⭐</option>
            </select>
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-1">Your Review</label>
            <textarea name="review" rows="4" required class="w-full border rounded-lg p-2" placeholder="Share your thoughts..."></textarea>
          </div>

          <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-800 transition">
            Submit Review
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ✅ JS for Tabs and Quantity -->
<script>
  const tabs = document.querySelectorAll('.tab-btn');
  const contents = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('text-gray-800','border-gray-900'));
      tabs.forEach(t => t.classList.add('text-gray-600'));
      tab.classList.add('text-gray-800','border-gray-900');
      const target = tab.getAttribute('data-tab');
      contents.forEach(c => c.classList.add('hidden'));
      document.getElementById(target).classList.remove('hidden');
    });
  });

  document.querySelectorAll('.increment').forEach(btn => {
    btn.addEventListener('click', e => {
      const input = e.target.closest('form').querySelector('.quantity');
      input.value = parseInt(input.value) + 1;
    });
  });

  document.querySelectorAll('.decrement').forEach(btn => {
    btn.addEventListener('click', e => {
      const input = e.target.closest('form').querySelector('.quantity');
      if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    });
  });

  // Toggle review box
  const toggleBtn = document.getElementById('toggleReviewBox');
  const reviewBox = document.getElementById('reviewBox');
  toggleBtn.addEventListener('click', () => {
    reviewBox.classList.toggle('hidden');
    reviewBox.classList.toggle('animate-fadeInUp');
  });
</script>

@endsection
