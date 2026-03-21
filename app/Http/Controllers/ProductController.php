<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const RECENT_KEY = 'recently_viewed';

    public function show(Product $product): View
    {
        $product->load(['reviews' => fn ($query) => $query->with('user:id,name')->latest()]);

        $this->remember($product);

        $related = Product::query()
            ->whereKeyNot($product->bmuk_no)
            ->where(fn ($query) => $query
                ->where(fn ($q) => $q->where('car_brand', $product->car_brand)->where('model', $product->model))
                ->orWhere('collection', $product->collection))
            ->with('reviews')
            ->orderByRaw('case when car_brand = ? and model = ? then 0 else 1 end', [$product->car_brand, $product->model])
            ->limit(4)
            ->get();

        $ratingCounts = $product->reviews->countBy('rating');

        return view('storefront.product', [
            'product' => $product,
            'related' => $related,
            'ratingCounts' => $ratingCounts,
            'inWishlist' => auth()->user()?->hasInWishlist($product->bmuk_no) ?? false,
            'recentlyViewed' => self::recentlyViewed()->reject(fn (Product $p) => $p->is($product))->take(4),
        ]);
    }

    /**
     * Products this visitor opened recently, newest first.
     *
     * @return Collection<int, Product>
     */
    public static function recentlyViewed(): Collection
    {
        $ids = session(self::RECENT_KEY, []);

        if ($ids === []) {
            return collect();
        }

        return Product::with('reviews')
            ->whereIn('bmuk_no', $ids)
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->bmuk_no, $ids))
            ->values();
    }

    private function remember(Product $product): void
    {
        $ids = collect(session(self::RECENT_KEY, []))
            ->reject(fn ($id) => (int) $id === $product->bmuk_no)
            ->prepend($product->bmuk_no)
            ->take(config('store.recently_viewed_limit'))
            ->values()
            ->all();

        session([self::RECENT_KEY => $ids]);
    }
}
