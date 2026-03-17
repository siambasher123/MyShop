<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review1;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review1::latest()->get();
        return view('admin.reviews', compact('reviews'));
    }
}
