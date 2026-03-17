@php
  use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-6">

  <!-- 🛍️ HEADER -->
  <div class="flex items-center justify-between mb-8">
    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
      🛒 Your Shopping Cart
    </h1>
  </div>

  @if(empty($cart))
    <div class="bg-white rounded-2xl shadow-lg p-10 text-center">
      <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" alt="Empty Cart" class="w-32 mx-auto mb-4 opacity-70">
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">Your cart is empty!</h2>
      <p class="text-gray-500 mb-6">Start exploring our collection to find something you love.</p>
      <a href="{{ route('shop.index') }}" class="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition font-semibold">
        🛍️ Go Shopping
      </a>
    </div>
  @else
  <!-- 🖤 CART TABLE -->
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
    <table class="w-full">
      <thead class="bg-gray-100 text-gray-700 uppercase text-sm font-semibold">
        <tr>
          <th class="px-6 py-3 text-left">Product</th>
          <th class="px-6 py-3 text-center">Quantity</th>
          <th class="px-6 py-3 text-right">Price</th>
          <th class="px-6 py-3 text-right">Total</th>
          <th class="px-6 py-3 text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cart as $id => $item)
        <tr class="border-t hover:bg-gray-50 transition">
          <!-- 🖼️ Product -->
          <td class="px-6 py-4 flex items-center space-x-4">
            @if(Str::startsWith($item['image'], 'http'))
              <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-lg object-cover shadow-sm">
            @else
              <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-lg object-cover shadow-sm">
            @endif
            <div>
              <p class="font-semibold text-gray-900">{{ $item['name'] }}</p>
              <p class="text-sm text-gray-500">#{{ $id }}</p>
            </div>
          </td>

          <!-- 🔢 Quantity -->
          <td class="px-6 py-4 text-center">
            <form action="{{ route('cart.update', $id) }}" method="POST" class="inline-flex items-center justify-center cart-form">
              @csrf
              <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                     class="w-16 border border-gray-300 rounded-lg text-center font-medium focus:ring-2 focus:ring-gray-700 focus:outline-none">
              <button type="submit"
                      class="ml-2 bg-black text-white px-3 py-1 rounded hover:bg-gray-800 transition text-sm font-semibold">
                Update
              </button>
            </form>
          </td>

          <!-- 💰 Price -->
          <td class="px-6 py-4 text-right text-gray-700 font-medium">
            ${{ number_format($item['price'], 2) }}
          </td>

          <!-- 🧮 Total -->
          <td class="px-6 py-4 text-right font-bold text-gray-900">
            ${{ number_format($item['price'] * $item['quantity'], 2) }}
          </td>

          <!-- ❌ Remove -->
          <td class="px-6 py-4 text-center">
            <form action="{{ route('cart.remove', $id) }}" method="POST" class="cart-form">
              @csrf @method('DELETE')
              <button type="submit"
                      class="text-red-600 hover:text-red-700 font-semibold transition">
                Remove
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- 🧾 CART SUMMARY -->
  <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

    <!-- LEFT: PROMO / NOTES -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900 mb-3">💡 Have a promo code?</h2>
      <form id="promo-form" class="flex">
        <input type="text" id="promo-code" placeholder="Enter code"
               class="w-full border border-gray-300 rounded-l-lg px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:outline-none uppercase">
        <button type="button" id="apply-promo"
                class="bg-black text-white px-4 py-2 rounded-r-lg hover:bg-gray-800 transition font-semibold">
          Apply
        </button>
      </form>
      <p id="promo-message" class="text-sm text-gray-500 mt-3">* Discounts will be applied automatically.</p>
    </div>

    <!-- RIGHT: TOTALS -->
    <div class="bg-gray-50 rounded-2xl shadow-inner p-6 border border-gray-200">
      @php
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
      @endphp

      <h2 class="text-xl font-bold text-gray-900 mb-4">🧾 Order Summary</h2>
      <div class="space-y-2 text-gray-700">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span id="subtotal" class="font-semibold">${{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="flex justify-between">
          <span>Discount</span>
          <span id="discount" class="font-semibold text-green-600">$0.00</span>
        </div>
        <hr class="my-3">
        <div class="flex justify-between text-lg font-bold text-gray-900">
          <span>Total</span>
          <span id="total">${{ number_format($subtotal, 2) }}</span>
        </div>
      </div>

      <div class="mt-6 text-right">
        <a href="{{ route('checkout') }}"
           class="inline-block bg-black text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transform hover:scale-[1.03] transition-all">
          Proceed to Checkout →
        </a>
      </div>
    </div>
  </div>
  @endif
</div>

{{-- 🚀 PROMO CODE SYSTEM --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const validCodes = {
      DISCOUNT5: 5, DISCOUNT8: 8, DISCOUNT10: 10, DISCOUNT12: 12,
      DISCOUNT15: 15, DISCOUNT20: 20, DISCOUNT25: 25, DISCOUNT30: 30,
      DISCOUNT40: 40, DISCOUNT50: 50
    };

    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discount');
    const totalEl = document.getElementById('total');
    const promoInput = document.getElementById('promo-code');
    const messageEl = document.getElementById('promo-message');
    const subtotal = parseFloat(subtotalEl.innerText.replace('$', ''));

    // Restore saved promo
    const savedPromo = localStorage.getItem('promoCode');
    if (savedPromo && validCodes[savedPromo]) {
      applyDiscount(savedPromo);
    }

    document.getElementById('apply-promo').addEventListener('click', () => {
      const code = promoInput.value.trim().toUpperCase();

      if (validCodes[code]) {
        localStorage.setItem('promoCode', code);
        applyDiscount(code);
      } else {
        localStorage.removeItem('promoCode');
        discountEl.innerText = `$0.00`;
        totalEl.innerText = `$${subtotal.toFixed(2)}`;
        messageEl.innerHTML = `<span class="text-red-600 font-semibold">❌ Invalid promo code.</span>`;
      }
    });

    // ✅ Apply discount and save to session
    function applyDiscount(code) {
      const percent = validCodes[code];
      const discountAmount = (subtotal * percent) / 100;
      const newTotal = subtotal - discountAmount;

      discountEl.innerText = `-$${discountAmount.toFixed(2)}`;
      totalEl.innerText = `$${newTotal.toFixed(2)}`;
      messageEl.innerHTML = `<span class="text-green-600 font-semibold">✅ ${percent}% discount applied!</span>`;
      promoInput.value = code;

      localStorage.setItem('promoCode', code);

      fetch("{{ route('cart.applyPromo') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
          code: code,
          percent: percent,
          discount: discountAmount,
          total: newTotal
        })
      });
    }

    // 🚨 Auto-remove promo if cart changes
    const cartForms = document.querySelectorAll('.cart-form');
    cartForms.forEach(form => {
      form.addEventListener('submit', () => {
        localStorage.removeItem('promoCode');
      });
    });
  });
</script>
@endsection
