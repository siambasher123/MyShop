<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $products = Product1::orderBy('id','desc')->paginate(10);

        // category → subcategories map for the UI
        $map = [
            'Men'        => ['T-Shirts','Jeans','Punjabi','Formal Shirts','Jackets'],
            'Women'      => ['Sarees','Kurtis','Tops','Dresses','Three-Pieces'],
            'Kids'       => ['Boys’ Wear','Girls’ Wear','Baby Wear','School Uniforms','Kids Accessories'],
            'Winter'     => ['Sweaters','Hoodies','Jackets','Scarves','Thermals'],
            'Jewellery'  => ['Necklaces','Earrings','Bracelets','Rings','Anklets'],
            'Shoes'      => ['Sneakers','Formal Shoes','Sandals','Sports Shoes','Boots'],
            'Home Decor' => ['Wall Art','Lamps','Cushions','Vases','Clocks'],
            'Perfumes'   => ['Men’s Perfumes','Women’s Perfumes','Unisex Fragrances','Body Mists','Deodorants'],
        ];

        return view('admin.products.index', compact('products','map'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category'    => 'required|string',
            'subcategory' => 'required|string',
            'code'        => 'required|string|max:100|unique:products1,code',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // store in storage/app/public/products
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product1::create($data);

        return redirect()->route('admin.products')->with('success', '✅ Product added successfully!');
    }

    public function edit(Product1 $product1)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // send the same map for the edit form
        $map = [
            'Men'        => ['T-Shirts','Jeans','Punjabi','Formal Shirts','Jackets'],
            'Women'      => ['Sarees','Kurtis','Tops','Dresses','Three-Pieces'],
            'Kids'       => ['Boys’ Wear','Girls’ Wear','Baby Wear','School Uniforms','Kids Accessories'],
            'Winter'     => ['Sweaters','Hoodies','Jackets','Scarves','Thermals'],
            'Jewellery'  => ['Necklaces','Earrings','Bracelets','Rings','Anklets'],
            'Shoes'      => ['Sneakers','Formal Shoes','Sandals','Sports Shoes','Boots'],
            'Home Decor' => ['Wall Art','Lamps','Cushions','Vases','Clocks'],
            'Perfumes'   => ['Men’s Perfumes','Women’s Perfumes','Unisex Fragrances','Body Mists','Deodorants'],
        ];

        return view('admin.products.edit', [
            'product' => $product1,
            'map'     => $map,
        ]);
    }

    public function update(Request $request, Product1 $product1)
    {
        $data = $request->validate([
            'category'    => 'required|string',
            'subcategory' => 'required|string',
            'code'        => 'required|string|max:100|unique:products1,code,' . $product1->id,
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product1->image) {
                Storage::disk('public')->delete($product1->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product1->update($data);

        return redirect()->route('admin.products')->with('success', '✅ Product updated successfully!');
    }

    public function destroy(Product1 $product1)
    {
        if ($product1->image) {
            Storage::disk('public')->delete($product1->image);
        }
        $product1->delete();

        return redirect()->route('admin.products')->with('success', '🗑️ Product deleted.');
    }
}
