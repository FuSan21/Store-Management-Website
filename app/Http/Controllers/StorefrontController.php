<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Notifications\ContactMessageReceived;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $bestSellerIds = OrderDetail::query()
            ->selectRaw('bmuk_no, sum(quantity) as sold')
            ->whereNotNull('bmuk_no')
            ->groupBy('bmuk_no')
            ->orderByDesc('sold')
            ->limit(8)
            ->pluck('bmuk_no');

        $bestSellers = Product::with('reviews')
            ->whereIn('bmuk_no', $bestSellerIds)
            ->get()
            ->sortBy(fn (Product $product) => $bestSellerIds->search($product->bmuk_no))
            ->values();

        return view('storefront.home', [
            'newArrivals' => Product::with('reviews')->latest()->limit(8)->get(),
            'bestSellers' => $bestSellers,
            'featured' => Product::with('reviews')->where('is_featured', true)->limit(4)->get(),
            'recentlyViewed' => ProductController::recentlyViewed(),
            'collections' => Product::query()
                ->selectRaw('collection, count(*) as products, min(price) as from_price')
                ->groupBy('collection')
                ->orderBy('collection')
                ->get(),
            'stats' => [
                'products' => Product::count(),
                'brands' => Product::distinct()->count('car_brand'),
            ],
        ]);
    }

    public function shop(): View
    {
        return view('storefront.shop');
    }

    public function about(): View
    {
        return view('storefront.about', [
            'brands' => Product::distinct()->orderBy('car_brand')->pluck('car_brand'),
        ]);
    }

    public function contact(): View
    {
        return view('storefront.contact');
    }

    public function sendMessage(ContactRequest $request)
    {
        Notification::route('mail', config('store.contact.email'))
            ->notify(new ContactMessageReceived($request->validated()));

        return back()->with('toast', ['type' => 'success', 'message' => 'Thanks — we reply within one working day.']);
    }
}
