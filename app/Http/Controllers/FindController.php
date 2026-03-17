<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product1;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FindController extends Controller
{
    private $categories = [
        'Men' => ['T-Shirts', 'Jeans', 'Punjabi', 'Formal Shirts', 'Jackets'],
        'Women' => ['Sarees', 'Kurtis', 'Tops', 'Dresses', 'Three-Pieces'],
        'Kids' => ['Boys’ Wear', 'Girls’ Wear', 'Baby Wear', 'School Uniforms', 'Accessories'],
        'Winter' => ['Sweaters', 'Hoodies', 'Jackets', 'Scarves', 'Thermals'],
        'Jewellery' => ['Necklaces', 'Earrings', 'Bracelets', 'Rings', 'Anklets'],
        'Shoes' => ['Sneakers', 'Formal Shoes', 'Sandals', 'Sports Shoes', 'Boots'],
        'Home Decor' => ['Wall Art', 'Lamps', 'Cushions', 'Vases', 'Clocks'],
        'Perfumes' => ['Men’s Perfumes', 'Women’s Perfumes', 'Unisex Fragrances', 'Body Mists', 'Deodorants'],
    ];

    public function index(Request $request)
    {
        $query = strtolower(trim($request->input('q', '')));
        $products = Product1::query();

        // 🧠 Step 1: Detect Price Filters
        $minPrice = null;
        $maxPrice = null;

        if (preg_match('/under\s*(\d+)/', $query, $m)) {
            $maxPrice = (float) $m[1];
        } elseif (preg_match('/between\s*(\d+)\D+(\d+)/', $query, $m)) {
            $minPrice = (float) $m[1];
            $maxPrice = (float) $m[2];
        } elseif (preg_match('/above\s*(\d+)/', $query, $m)) {
            $minPrice = (float) $m[1];
        }

        //  Step 2: Detect Category + Subcategory
        $detectedCategory = null;
        $detectedSubcategory = null;

        foreach ($this->categories as $category => $subs) {
            if (str_contains($query, strtolower($category))) {
                $detectedCategory = $category;
            }

            foreach ($subs as $sub) {
                $cleanSub = strtolower(str_replace(['’', "'", ' '], '', $sub));
                $cleanQuery = str_replace(['’', "'", ' '], '', $query);

                if (str_contains($cleanQuery, $cleanSub)) {
                    $detectedSubcategory = $sub;
                    $detectedCategory = $category;
                }
            }
        }

        // Step 3: Apply Filters
        if ($minPrice !== null && $maxPrice !== null) {
            $products->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($maxPrice !== null) {
            $products->where('price', '<=', $maxPrice);
        } elseif ($minPrice !== null) {
            $products->where('price', '>=', $minPrice);
        }

        if ($detectedCategory) $products->where('category', $detectedCategory);
        if ($detectedSubcategory) $products->where('subcategory', $detectedSubcategory);

        // Step 4: Fallback Keyword Search
        if (!$detectedCategory && !$detectedSubcategory) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                    ->orWhere('description', 'LIKE', "%$query%");
            });
        }

        // 🧠 Step 5: Fetch results
        $results = $products->limit(40)->get();

        // 🧠 Step 6: Optional AI fallback
        if ($results->isEmpty()) {
            $aiSuggestion = $this->aiAssist($query);
            if ($aiSuggestion) {
                $results = Product1::where('category', 'LIKE', "%{$aiSuggestion}%")
                    ->limit(40)
                    ->get();
            }
        }

        // Step 7: No image cleaning — Blade handles it
        return view('find.results', [
            'results' => $results,
            'query' => $query,
        ]);
    }

    //  AI Fallback (Gemini)
    private function aiAssist($query)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) return null;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";
        $prompt = "Given this shopping search: '$query', return only ONE category or subcategory name (like Men, Women, Shoes, Perfumes).";

        $payload = [
            'contents' => [[
                'parts' => [
                    ['text' => "You output only one valid category or subcategory name from the given list."],
                    ['text' => $prompt]
                ]
            ]]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result, true);
        return trim($json['candidates'][0]['content']['parts'][0]['text'] ?? '');
    }

    // 💬 Chatbot (Gemini)



public function chat(Request $request)
{
    $userMessage = strtolower(trim($request->input('message')));
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        return response()->json(['reply' => '⚠️ Missing Gemini API key.']);
    }

    //  1. Detect product-related questions
    $productKeywords = [
        'buy', 'have', 'sell', 'price', 'under', 'above', 'between',
        'product', 'necklace', 'shoes', 'perfume', 't-shirt', 'jeans',
        'saree', 'sarees', 'ring', 'bracelet', 'hoodie', 'jacket',
        'sweater', 'dress', 'dresses', 'store', 'shop', 'item'
    ];

    // Check if this message looks like a shopping intent
    $isProductQuery = collect($productKeywords)
        ->contains(fn($word) => str_contains($userMessage, $word))
        && !str_contains($userMessage, 'what is')   // Exclude general meaning questions
        && !str_contains($userMessage, 'who')       // Exclude non-shopping questions
        && !str_contains($userMessage, 'about');    // Exclude general "about" questions

    //  2. Product-related → search database
    if ($isProductQuery) {
        try {
            $products = Product1::query();

            // Extract main keyword (e.g. "show sarees" → "sarees")
            $keyword = collect(explode(' ', $userMessage))
                ->reject(fn($word) => in_array($word, [
                    'show', 'buy', 'have', 'sell', 'under', 'above', 'between',
                    'product', 'products', 'items', 'store', 'shop', 'price', 'about'
                ]))
                ->implode(' ');

            if (empty(trim($keyword))) {
                $keyword = $userMessage;
            }

            // Detect price filters
            $minPrice = null;
            $maxPrice = null;
            if (preg_match('/under\s*(\d+)/', $userMessage, $m)) {
                $maxPrice = (float) $m[1];
                $products->where('price', '<=', $maxPrice);
            } elseif (preg_match('/between\s*(\d+)\D+(\d+)/', $userMessage, $m)) {
                $minPrice = (float) $m[1];
                $maxPrice = (float) $m[2];
                $products->whereBetween('price', [$minPrice, $maxPrice]);
            } elseif (preg_match('/above\s*(\d+)/', $userMessage, $m)) {
                $minPrice = (float) $m[1];
                $products->where('price', '>=', $minPrice);
            }

            // Keyword match
            $products->where(function ($q) use ($keyword) {
                $q->where('category', 'LIKE', "%$keyword%")
                    ->orWhere('subcategory', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%")
                    ->orWhere('code', 'LIKE', "%$keyword%");
            });

            $results = $products->limit(5)->get();

            if ($results->isEmpty()) {
                Log::warning('Product search returned no results', [
                    'query' => $userMessage,
                    'keyword' => $keyword,
                    'filters' => ['min' => $minPrice, 'max' => $maxPrice],
                ]);

                return response()->json([
                    'reply' => "😔 Sorry, I couldn’t find any products matching that description."
                ]);
            }

            //  Build numbered reply
            $reply = "🛍️ Here are some products I found for you:\n\n";
            foreach ($results as $index => $p) {
                $num = $index + 1;
                $reply .= "{$num}. **{$p->subcategory}** — \${$p->price}\n";
                $reply .= "   Category: {$p->category}\n";
                if ($p->description) {
                    $reply .= "   {$p->description}\n";
                }
                if ($p->image) {
                    $reply .= "   🖼️ Image: {$p->image}\n";
                }
                $reply .= "--------\n";
            }

            return response()->json(['reply' => $reply]);

        } catch (\Throwable $e) {
            Log::error('Product query failed', ['error' => $e->getMessage()]);
            return response()->json([
                'reply' => ' Database error: ' . $e->getMessage()
            ]);
        }
    }

    //  3. Otherwise → use Gemini for general chat (SSL disabled for local use)
    try {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

        $payload = [
            'contents' => [[
                'parts' => [
                    ['text' => "You are a friendly AI assistant for MyShop. 
                    If the user asks about MyShop, explain it is an online store offering various products for Men, Women, Kids, and Home Decor.
                    For general questions, answer normally and politely."],
                    ['text' => $userMessage]
                ]
            ]]
        ];

        $response = Http::withOptions(['verify' => false])
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json([
                'reply' => " Gemini API error ({$response->status()}): {$response->body()}"
            ]);
        }

        $json = $response->json();
        $reply = $json['candidates'][0]['content']['parts'][0]['text']
            ?? ' Unexpected response from Gemini.';

        return response()->json(['reply' => trim($reply)]);
    } catch (\Throwable $e) {
        Log::error('Gemini connection problem', ['error' => $e->getMessage()]);
        return response()->json([
            'reply' => ' Connection problem: ' . $e->getMessage()
        ]);
    }
}


}
