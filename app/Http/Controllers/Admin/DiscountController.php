<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product1;

class DiscountController extends Controller
{
    public function index()
    {
        $categories = Product1::select('category')->distinct()->pluck('category');
        $subcategories = Product1::select('subcategory')->distinct()->pluck('subcategory');

        return view('admin.discounts.index', compact('categories', 'subcategories'));
    }

    public function applyDiscount(Request $request)
    {
        $validated = $request->validate([
            'discount_type' => 'required|string',
            'discount_percent' => 'required|numeric|min:1|max:90',
        ]);

        // 🧮 Convert percentage to decimal
        $discountDecimal = $validated['discount_percent'] / 100;

        // 🧩 Define reusable logic
        $applyDiscount = function ($query) use ($discountDecimal) {
            // Store old price first, then apply discount
            $query->update([
                'old_price' => DB::raw('price'),
                'price'     => DB::raw("price - (price * $discountDecimal)")
            ]);
        };

        // 🏷️ Apply by category
        if ($request->discount_type === 'category' && $request->filled('category')) {
            $applyDiscount(Product1::where('category', $request->category));
        }

        // 🏷️ Apply by subcategory
        elseif ($request->discount_type === 'subcategory' && $request->filled(['category', 'subcategory'])) {
            $applyDiscount(Product1::where('category', $request->category)
                                   ->where('subcategory', $request->subcategory));
        }

        // 🏷️ Apply by individual product (code string)
        elseif ($request->discount_type === 'product' && $request->filled('product_id')) {
            $applyDiscount(Product1::where('code', $request->product_id));
        }

        // 🚫 Invalid input
        else {
            return redirect()->back()->with('error', '⚠️ Please select valid discount options.');
        }

        // ✅ Success
        return redirect()->route('admin.discounts')->with('success', '✅ Discount applied successfully!');
    }
}
