<?php

namespace App\Services;

class NlpQueryParser
{
    // Canonical categories in your DB
    protected array $categories = [
        'Men','Women','Kids','Winter','Jewellery','Shoes','Home Decor','Perfumes',
    ];

    // Synonyms/typos -> canonical category
    protected array $categorySynonyms = [
        'men' => 'Men',
        'man' => 'Men',
        'male' => 'Men',

        'women' => 'Women',
        'woman' => 'Women',
        'ladies' => 'Women',
        'eomen' => 'Women', // typo

        'kids' => 'Kids',
        'kid' => 'Kids',
        'children' => 'Kids',

        'winter' => 'Winter',

        'jewellery' => 'Jewellery',
        'jewelry' => 'Jewellery',
        'jwellerys' => 'Jewellery', // typo

        'shoes' => 'Shoes',

        'home decor' => 'Home Decor',
        'homedecor' => 'Home Decor',
        'home-decor' => 'Home Decor',

        'perfume' => 'Perfumes',
        'perfumes' => 'Perfumes',
        'fragrance' => 'Perfumes',
        'fragrances' => 'Perfumes',
    ];

    // Subcategory synonyms/typos -> canonical subcategory
    protected array $subcategorySynonyms = [
        // MEN
        't-shirt' => ['t-shirt','tshirts','tshirt','t shirt','tees','tee'],
        'jeans' => ['jeans','jean','jens'], // jens = typo
        'punjabi' => ['punjabi','panjabi'],
        'formal shirts' => ['formal shirts','formal shirt'],
        'jackets' => ['jackets','jacket'],

        // WOMEN
        'sarees' => ['sarees','saree','sharees'],                // sharees = typo
        'kurtis' => ['kurti','kurtis','kurits'],                 // kurits = typo
        'tops' => ['tops','top'],
        'dresses' => ['dresses','dress'],
        'three pieces' => ['three pieces','three-piece','three piece'],

        // KIDS
        'boys wear' => ['boys wear','boy wear','boys’ wear','boys\' wear'],
        'girls wear' => ['girls wear','girl wear','girls’ wear','girls\' wear','girls wears'],
        'baby wear' => ['baby wear','babys wear'],               // babys = typo
        'school uniform' => ['school uniform','school uniforms'],
        'accessories' => ['accessories','kids accessories','assoccery','assocceries'],

        // WINTER
        'sweaters' => ['sweater','sweaters'],
        'hoodies' => ['hoodie','hoodies'],
        'jackets' => ['jackets','jacket'],
        'scarves' => ['scarves','scarf','scarfs','scarvers'],    // scarvers = typo
        'thermals' => ['thermal','thermals'],

        // JEWELLERY
        'necklaces' => ['necklaces','necklace','neckleces'],     // neckleces = typo
        'earrings' => ['earrings','earring','earings'],          // earings = typo
        'bracelets' => ['bracelets','bracelet','braceletes'],    // braceletes = typo
        'rings' => ['rings','ring'],
        'anklets' => ['anklets','anklet'],

        // SHOES
        'sneakers' => ['sneakers','sneaker'],
        'formal shoes' => ['formal shoes','formal shoe'],
        'sandals' => ['sandals','sandal'],
        'sports shoes' => ['sports shoes','sport shoes','sport shoe','sports shoe'],
        'boots' => ['boots','boot'],

        // HOME DECOR
        'wall art' => ['wall art','wall-art'],
        'lamps' => ['lamps','lamp'],
        'cushions' => ['cushions','cushion','caushions'],        // caushions = typo
        'vases' => ['vases','vase','vasels'],                    // vasels = typo
        'clocks' => ['clocks','clock'],

        // PERFUMES
        'mens perfume' => ['mens perfume','men perfume','men’s perfume'],
        'women perfume' => ['women perfume','womens perfume','woman perfume','eomen perfume'], // typo
        'unisex perfume' => ['unisex perfume','unisex'],
        'body mists' => ['body mists','body mist'],
        'deodorants' => ['deodorants','deodorant'],
    ];

    protected array $stopwords = [
        'show','me','find','get','the','a','an','for','of','to','and','or','in','on',
        'under','below','less','than','over','above','between','from','upto','up','to',
        'with','without','dollar','dollars','usd','tk','bdt','$','৳',
        // categories in generic form (we’ll handle them via mappings)
        'men','man','male','women','woman','ladies','kids','kid','children',
        'winter','jewellery','jewelry','shoes','home','decor','home-decor','homedecor',
        'perfume','perfumes','fragrance','fragrances'
    ];

    public function parse(string $query): array
    {
        $raw = strtolower(trim($query));

        $filters = [
            'category'   => null,
            'subcategory'=> null,
            'min_price'  => null,
            'max_price'  => null,
            'in_stock'   => null,
            'keywords'   => [],
        ];

        // ---- Category detection
        $catHit = $this->findCategory($raw);
        if ($catHit) {
            $filters['category'] = $catHit;
        }

        // ---- Subcategory detection (normalize synonyms/typos)
        $subHit = $this->findSubcategory($raw);
        if ($subHit) {
            $filters['subcategory'] = $subHit;
        }

        // ---- Price detection (supports: under/below/less than/over/above/between X and Y, currency words/symbols)
        $this->extractPrices($raw, $filters);

        // ---- Stock flag
        if (str_contains($raw, 'in stock'))  $filters['in_stock'] = true;
        if (str_contains($raw, 'out of stock')) $filters['in_stock'] = false;

        // ---- Keywords (remove punctuation, numbers, stopwords, currency words we used)
        $keywords = preg_replace('/[^a-z0-9\s\$৳\.]/', ' ', $raw);
        $tokens   = preg_split('/\s+/', $keywords);

        $throwAway = array_merge(
            $this->stopwords,
            // numbers If you want to keep numbers sometimes, remove this line
            array_map(fn($n) => (string)$n, range(0,10000))
        );

        $tokens = array_values(array_filter($tokens, function ($t) use ($throwAway) {
            return $t !== '' && !in_array($t, $throwAway, true);
        }));

        // If we already locked a subcategory, prefer to strip variants of it from keywords:
        if ($subHit) {
            foreach ($this->subcategorySynonyms[$subHit] ?? [] as $syn) {
                $tokens = array_values(array_filter($tokens, fn($t) => $t !== str_replace(' ', '', $syn) && $t !== $syn));
            }
        }

        // Also drop the literal words we used to detect price
        $tokens = array_values(array_filter($tokens, fn($t) => !in_array($t, ['under','below','less','than','over','above','between','and','to','upto'], true)));

        $filters['keywords'] = $tokens;

        return $filters;
    }

    protected function findCategory(string $q): ?string
    {
        // First try exact/phrase matches from synonyms
        foreach ($this->categorySynonyms as $needle => $canon) {
            if (preg_match('/\b' . preg_quote($needle, '/') . '\b/', $q)) {
                return $canon;
            }
        }
        // Then try canonical names (just in case)
        foreach ($this->categories as $cat) {
            if (preg_match('/\b' . preg_quote(strtolower($cat), '/') . '\b/', $q)) {
                return $cat;
            }
        }
        return null;
    }

    protected function findSubcategory(string $q): ?string
    {
        foreach ($this->subcategorySynonyms as $canonical => $list) {
            foreach ($list as $syn) {
                // allow hyphen/space variations: t shirt / t-shirt / tshirt
                $pattern = str_replace(' ', '[- ]?', preg_quote($syn, '/'));
                if (preg_match("/\b{$pattern}\b/", $q)) {
                    return $canonical;
                }
            }
        }
        return null;
    }

    protected function extractPrices(string $q, array &$filters): void
    {
        // between X and Y
        if (preg_match('/between\s*(\d+(?:\.\d+)?)\s*(?:and|to)\s*(\d+(?:\.\d+)?)/', $q, $m)) {
            $filters['min_price'] = (float)$m[1];
            $filters['max_price'] = (float)$m[2];
            return;
        }

        // under/below/less than/up to
        if (preg_match('/(?:under|below|less\s+than|up\s*to|<=?)\s*\$?\s*(\d+(?:\.\d+)?)/', $q, $m)) {
            $filters['max_price'] = (float)$m[1];
            return;
        }

        // over/above/more than
        if (preg_match('/(?:over|above|more\s+than|>=?)\s*\$?\s*(\d+(?:\.\d+)?)/', $q, $m)) {
            $filters['min_price'] = (float)$m[1];
            return;
        }

        // currency after number: "10 dollars / 10 usd / 10 tk / 10 bdt / 10 ৳"
        if (preg_match('/\b(\d+(?:\.\d+)?)\s*(?:dollars?|usd|tk|bdt|৳)\b/', $q, $m)) {
            // we’ll treat it as a max_price if wording suggests budget style:
            $filters['max_price'] = (float)$m[1];
        }

        // currency before number: "$10 / ৳500"
        if (preg_match('/[\$৳]\s*(\d+(?:\.\d+)?)/', $q, $m)) {
            $filters['max_price'] = (float)$m[1];
        }
    }
}
