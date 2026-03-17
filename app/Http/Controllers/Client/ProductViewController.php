<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product1;
use Illuminate\Http\Request;

class ProductViewController extends Controller
{
    public function index()
    {
        $categories = Product1::select('category')->distinct()->pluck('category');
        return view('client.shop.index', compact('categories'));
    }

    public function category($category, Request $request)
    {
        // ✅ Decode the URL value
        $category = urldecode($category);

        $query = Product1::where('category', $category);

        if ($request->filled('stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        $products = $query->get();
        $subcategories = Product1::where('category', $category)->select('subcategory')->distinct()->pluck('subcategory');

        return view('client.shop.category', compact('category', 'products', 'subcategories'));
    }

    public function subcategory($category, $subcategory)
    {
        // ✅ Decode both
        $category = urldecode($category);
        $subcategory = urldecode($subcategory);

        $products = Product1::where('category', $category)
            ->where('subcategory', $subcategory)
            ->get();

        return view('client.shop.subcategory', compact('category', 'subcategory', 'products'));
    }

    public function show($id)
    {
        $product = Product1::findOrFail($id);
        return view('client.shop.product', compact('product'));
    }
}
