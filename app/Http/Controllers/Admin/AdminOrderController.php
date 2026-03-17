<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order1;
use App\Models\Transaction1;

class AdminOrderController extends Controller
{
    /**
     * 🧾 Display all orders.
     */
    public function index()
    {
        $orders = Order1::orderBy('created_at', 'desc')->get();

        // Decode any JSON fields safely
        foreach ($orders as $order) {
            $order->decoded_products = is_string($order->products)
                ? json_decode($order->products, true)
                : $order->products;

            $order->decoded_codes = is_string($order->product_codes)
                ? json_decode($order->product_codes, true)
                : $order->product_codes;
        }

        return view('admin.orders', compact('orders'));
    }

    /**
     * ✅ Handle "Accept" or "Reject" order.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order1::findOrFail($id);
        $status = $request->input('status');

        // Convert to proper status labels
        if ($status === 'accepted' || $status === 'yes') {
            $order->status = 'accepted';
        } elseif ($status === 'rejected' || $status === 'no') {
            $order->status = 'rejected';
        } else {
            $order->status = $status;
        }

        $order->save();

        /**
         * 💾 If accepted — copy all order data into transactions1
         */
        if ($order->status === 'accepted') {
            // Check if this order already exists in transactions1 (avoid duplicates)
            $exists = Transaction1::where('order_id', $order->id)->exists();

            if (!$exists) {
                // Decode product JSON safely
                $products = is_string($order->products)
                    ? json_decode($order->products, true)
                    : $order->products;

                $totalQty = collect($products ?? [])->sum('quantity');

                Transaction1::create([
                    'order_id'      => $order->id,
                    'user_id'       => $order->user_id,
                    'full_name'     => $order->full_name,
                    'email'         => $order->email,
                    'mobile_number' => $order->mobile_number,
                    'delivery_area' => $order->delivery_area,
                    'address'       => $order->address,
                    'product_codes' => $order->product_codes,
                    'products'      => $order->products,
                    'quantity'      => $totalQty,
                    'total'         => $order->total,
                    'status'        => 'pending', // default until admin marks done
                ]);
            }
        }

        /**
         * ❌ If rejected — remove it from transactions1
         */
        if ($order->status === 'rejected') {
            Transaction1::where('order_id', $order->id)->delete();
        }

        return redirect()
            ->route('admin.orders')
            ->with('success', 'Order status updated successfully.');
    }
}
