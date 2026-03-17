@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-6">

  {{-- ✅ Flash Messages --}}
  @if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
      <strong class="font-bold">Success!</strong>
      <span class="block sm:inline">{{ session('success') }}</span>
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm" role="alert">
      <strong class="font-bold">Error!</strong>
      <span class="block sm:inline">{{ session('error') }}</span>
    </div>
  @endif

  {{-- 🧩 Auto-hide Flash Messages --}}
  <script>
    setTimeout(() => {
      document.querySelectorAll('[role="alert"]').forEach(el => {
        el.style.transition = "opacity 0.5s ease";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
      });
    }, 3000);
  </script>

  {{-- 🔙 BACK TO CART --}}
  <div class="flex items-center mb-8">
    <a href="{{ route('cart.index') }}" 
       class="flex items-center bg-black text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-800 transition shadow-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Back to Cart
    </a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <!-- LEFT: Customer info -->
    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
      <h2 class="text-3xl font-bold mb-6 text-gray-900 tracking-tight">📝 Checkout Details</h2>

      <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form" class="space-y-5">
        @csrf
        <input type="hidden" name="discount_percent" id="discount_percent" value="0">

        <div>
          <label class="block text-gray-700 font-medium mb-1">Full Name</label>
          <input name="full_name" type="text"
                 class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-black focus:outline-none transition"
                 placeholder="Enter your full name" required>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-1">Mobile Number</label>
          <input name="mobile_number" type="text"
                 class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-black focus:outline-none transition"
                 placeholder="e.g. +1 234 567 890" required>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Delivery Area</label>
          <div class="space-y-3">
            <label class="flex items-center cursor-pointer">
              <input type="radio" name="delivery_area" value="dhaka_outside" checked class="accent-black mr-3 area-option">
              <span class="text-gray-700">Outside Dhaka City ($1.20)</span>
            </label>
            <label class="flex items-center cursor-pointer">
              <input type="radio" name="delivery_area" value="dhaka_inside" class="accent-black mr-3 area-option">
              <span class="text-gray-700">Inside Dhaka City ($0.60)</span>
            </label>
            <label class="flex items-center cursor-pointer">
              <input type="radio" name="delivery_area" value="other" class="accent-black mr-3 area-option">
              <span class="text-gray-700">Other Areas ($1.00)</span>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-1">Full Address</label>
          <textarea name="address"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-black focus:outline-none transition"
                    rows="3" placeholder="e.g. House #12, Road #3, Dhanmondi" required></textarea>
        </div>

        <button
          class="w-full bg-black text-white py-3 rounded-lg hover:bg-gray-800 transition font-semibold text-lg shadow-md hover:shadow-lg transform hover:scale-[1.02]">
          Place Order →
        </button>
      </form>
    </div>

    <!-- RIGHT: Order Summary -->
    <div class="bg-gray-50 rounded-2xl shadow-inner p-8 border border-gray-200" id="order-summary">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">🛍️ Order Summary</h2>
      </div>

      <table class="w-full text-left text-gray-800">
        <thead>
          <tr class="border-b border-gray-300 text-sm uppercase text-gray-600">
            <th class="pb-2">Product</th>
            <th class="pb-2 text-center">Qty</th>
            <th class="pb-2 text-right">Price</th>
            <th class="pb-2 text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          @php $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']); @endphp
          @foreach($cart as $id => $item)
          <tr class="border-b border-gray-200 hover:bg-gray-100 transition">
            <td class="py-3 font-medium">{{ $item['name'] }}</td>
            <td class="text-center">{{ $item['quantity'] }}</td>
            <td class="text-right">${{ number_format($item['price'], 2) }}</td>
            <td class="text-right font-semibold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-6 space-y-2 text-gray-800">
        <div class="flex justify-between text-base">
          <span>Subtotal</span>
          <span class="font-semibold" id="subtotal">${{ number_format($subtotal, 2) }}</span>
        </div>

        {{-- Discount Section (hidden until promo applied) --}}
        <div class="flex justify-between text-base hidden" id="discount-row">
          <span>Discount (<span id="discount-percent-text"></span>%)</span>
          <span class="font-semibold text-green-600" id="discount-amount">-$0.00</span>
        </div>

        <div class="flex justify-between text-base">
          <span>Shipping</span>
          <span class="font-semibold" id="shipping">$1.20</span>
        </div>

        <hr class="my-3 border-gray-300">
        <div class="flex justify-between text-lg font-bold text-gray-900">
          <span>Total Payable</span>
          <span id="total">${{ number_format($subtotal + 1.20, 2) }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- 🚀 Dynamic Shipping + Discount Sync --}}
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const subtotalEl = document.getElementById('subtotal');
    const shippingEl = document.getElementById('shipping');
    const totalEl = document.getElementById('total');
    const discountEl = document.getElementById('discount-amount');
    const discountRow = document.getElementById('discount-row');
    const discountPercentText = document.getElementById('discount-percent-text');
    const discountField = document.getElementById('discount_percent');

    const subtotal = parseFloat(subtotalEl.innerText.replace('$',''));
    const areaPrices = { dhaka_inside: 0.60, dhaka_outside: 1.20, other: 1.00 };

    // ✅ Load promo from localStorage
    const savedPromo = localStorage.getItem('promoCode');
    const validCodes = {
      DISCOUNT5: 5, DISCOUNT8: 8, DISCOUNT10: 10, DISCOUNT12: 12,
      DISCOUNT15: 15, DISCOUNT20: 20, DISCOUNT25: 25, DISCOUNT30: 30,
      DISCOUNT40: 40, DISCOUNT50: 50
    };

    let discountPercent = 0;
    if (savedPromo && validCodes[savedPromo]) {
      discountPercent = validCodes[savedPromo];
      discountField.value = discountPercent;
      discountPercentText.innerText = discountPercent;
      discountRow.classList.remove('hidden');
    }

    const updateTotal = (shipping) => {
      const discountAmount = (subtotal * discountPercent) / 100;
      const total = subtotal - discountAmount + shipping;
      discountEl.innerText = `-$${discountAmount.toFixed(2)}`;
      totalEl.innerText = `$${total.toFixed(2)}`;
    };

    // Initial total calculation
    updateTotal(1.20);

    // Handle area change
    document.querySelectorAll('.area-option').forEach(radio => {
      radio.addEventListener('change', e => {
        const shipping = areaPrices[e.target.value];
        shippingEl.innerText = `$${shipping.toFixed(2)}`;
        updateTotal(shipping);
      });
    });
  });
</script>
@endsection
