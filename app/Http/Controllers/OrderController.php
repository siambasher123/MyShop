<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order1;

class OrderController extends Controller
{
    /**
     *  Show the checkout page.
     */
    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn($item) => (float)$item['price'] * (int)$item['quantity']);

        // Optional: read promo from session (if you set it from cart)
        $discountPercent = (int) session('discount_percent', 0);
        $discountAmount  = (float) session('discount_amount', 0);
        $discountedTotal = (float) session('discounted_total', $subtotal);

        return view('client.checkout', compact('cart', 'subtotal', 'discountPercent', 'discountAmount', 'discountedTotal'));
    }

    /**
     *  Handle checkout submission and store order.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'mobile_number'  => 'required|string|max:20',
            'delivery_area'  => 'required|string',
            'address'        => 'required|string|max:500',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Subtotal from cart
        $subtotal = collect($cart)->sum(fn($item) => (float)$item['price'] * (int)$item['quantity']);

        // Shipping (your UI shows dollars; keep consistent here)
        $shipping = match ($request->delivery_area) {
            'dhaka_inside' => 0.60,
            'other'        => 1.00,
            default        => 1.20,
        };

        // Discount (from hidden input or session)
        $discountPercent = (int)($request->input('discount_percent', session('discount_percent', 0)));
        $discount = ($subtotal * $discountPercent) / 100;

        // Final total (this is what you see as "Total Payable" on checkout)
        $finalTotal = round($subtotal - $discount + $shipping, 2);

        // Create one product code per cart line item
        $productCodes = [];
        foreach ($cart as $pid => $item) {
            // e.g. ORD-7-AB12CD34 (include product id for readability)
            $productCodes[] = 'ORD-' . $pid . '-' . strtoupper(substr(md5(uniqid((string)$pid, true)), 0, 8));
        }

        // Save order (NO shipping_charge column used)
        Order1::create([
            'user_id'         => Auth::id(),
            'full_name'       => $request->full_name,
            'email'           => Auth::user()->email ?? 'guest@example.com',
            'mobile_number'   => $request->mobile_number,
            'delivery_area'   => $request->delivery_area,
            'address'         => $request->address,
            'products'        => json_encode($cart, JSON_UNESCAPED_UNICODE),
            'product_codes'   => json_encode($productCodes, JSON_UNESCAPED_UNICODE),

            // Per your request: "subtotal is total"
            'subtotal'        => $finalTotal,
            'total'           => $finalTotal,

            'status'          => 'pending',
        ]);

        // Clear cart
        session()->forget(['cart', 'promo_code', 'discount_percent', 'discount_amount', 'discounted_total']);

        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}
