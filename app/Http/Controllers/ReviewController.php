<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review1;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'subcategory' => 'required|string',
            'code' => 'required|string',
            'price' => 'required|numeric',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        Review1::create([
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'code' => $request->code,
            'price' => $request->price,
            'username' => Auth::user()->name ?? Auth::user()->email ?? 'Anonymous',
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Thank you for your review!');
    }
}
