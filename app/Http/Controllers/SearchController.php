<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product1;
use App\Services\NlpQueryParser;

class SearchController extends Controller
{
    public function search(Request $request, NlpQueryParser $parser)
    {
        $q = trim((string)$request->get('q', ''));
        if ($q === '') {
            return redirect()->route('shop.index');
        }

        $filters = $parser->parse($q);

        $query = Product1::query();

        // category (case-insensitive)
        if (!empty($filters['category'])) {
            $cat = strtolower($filters['category']);
            $query->whereRaw('LOWER(category) LIKE ?', ["%{$cat}%"]);
        }

        // subcategory (match canonical name found by parser)
        if (!empty($filters['subcategory'])) {
            $sub = strtolower($filters['subcategory']);
            $query->whereRaw('LOWER(subcategory) LIKE ?', ["%{$sub}%"]);
        }

        // min/max price
        if (!is_null($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!is_null($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // stock
        if ($filters['in_stock'] === true) {
            $query->where('stock', '>', 0);
        } elseif ($filters['in_stock'] === false) {
            $query->where('stock', '<=', 0);
        }

        // OR keyword search across subcategory/description/code (NOT one AND per token)
        $keywords = $filters['keywords'] ?? [];
        if (!empty($keywords)) {
            $query->where(function ($qq) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = trim($kw);
                    if ($kw === '') continue;
                    $qq->orWhereRaw('LOWER(subcategory) LIKE ?', ["%{$kw}%"])
                       ->orWhereRaw('LOWER(description) LIKE ?', ["%{$kw}%"])
                       ->orWhereRaw('LOWER(code) LIKE ?', ["%{$kw}%"]);
                }
            });
        }

        $products = $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('client.shop.search', [
            'q'        => $q,
            'filters'  => $filters,
            'products' => $products,
        ]);
    }
}
