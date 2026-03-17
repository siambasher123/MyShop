<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\InquiryController; //  Contact inquiries
use App\Http\Controllers\Admin\ClientController;   //  Registered clients
use App\Http\Controllers\Admin\AdminReviewController; //   Reviews controller
use App\Http\Controllers\Client\ProductViewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FindController; //   AI-Powered Search Controller
use App\Http\Controllers\ReviewController; //  Added Review Controller

//  Home Page
Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// AJAX Email Duplicate Check
Route::get('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');

// AJAX Email Validation for Forgot Password
Route::post('/check-email', function (Request $request) {
    $exists = \App\Models\User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
});

// Admin Routes (Protected + Role Checked)
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    //  Product Management
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product1}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product1}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product1}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    //  Discount Management
    Route::get('/discounts', [DiscountController::class, 'index'])->name('admin.discounts');
    Route::post('/discounts/apply', [DiscountController::class, 'applyDiscount'])->name('admin.discounts.apply');

    //  Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::post('/orders/update/{id}', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update');

    // Transactions Management
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('admin.transactions');
    Route::post('/transactions/note/{id}', [AdminTransactionController::class, 'updatePaymentNote'])->name('admin.transactions.note');
    Route::post('/transactions/status/{id}', [AdminTransactionController::class, 'updateStatus'])->name('admin.transactions.status');

    // Reviews Management (NEW)
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews');

    //  Contact Inquiries
    Route::get('/inquiries', [InquiryController::class, 'index'])->name('admin.inquiries');
    Route::post('/inquiries/reply/{id}', [InquiryController::class, 'reply'])->name('admin.inquiries.reply');

    //  Registered Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('admin.clients');
});

//  Client Product Pages
Route::prefix('shop')->group(function () {
    Route::get('/', [ProductViewController::class, 'index'])->name('shop.index');
    Route::get('/category/{category}', [ProductViewController::class, 'category'])->name('shop.category');
    Route::get('/category/{category}/{subcategory}', [ProductViewController::class, 'subcategory'])->name('shop.subcategory');
    Route::get('/product/{id}', [ProductViewController::class, 'show'])->name('shop.product');
});

//  Contact Us Page
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

//  Product Reviews (NEW)
Route::post('/product/review', [ReviewController::class, 'store'])->name('review.store')->middleware('auth');

// Traditional Search
Route::get('/search', [SearchController::class, 'search'])->name('search');

//  AI-Powered Natural Language Search (FindController)
Route::get('/find', [FindController::class, 'index'])->name('find');

//  AI Chatbot Interaction
Route::post('/ai-chat', [FindController::class, 'chat'])->name('ai.chat');

//  Cart System
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/delete/{id}', [CartController::class, 'remove'])->name('cart.remove');

//  Apply Promo (AJAX)
Route::post('/cart/apply-promo', function (Request $request) {
    session([
        'promo_code' => $request->code,
        'discount_percent' => $request->percent,
        'discount_amount' => $request->discount,
        'discounted_total' => $request->total,
    ]);
    return response()->json(['success' => true]);
})->name('cart.applyPromo');

//  Checkout System
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout/place', [OrderController::class, 'placeOrder'])->name('checkout.place');

//  FIX: Handle accidental GET request to /checkout/place
Route::get('/checkout/place', function () {
    return redirect()->route('checkout')
        ->with('error', 'Please submit your order using the checkout form.');
});
