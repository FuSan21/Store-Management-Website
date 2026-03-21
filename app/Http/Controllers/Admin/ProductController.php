<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AdjustStock;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    private const SORTS = ['bmuk_no', 'price', 'stock', 'car_brand', 'created_at'];

    public function index(Request $request): View
    {
        $sort = in_array($request->input('sort'), self::SORTS, true) ? $request->input('sort') : 'bmuk_no';
        $direction = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $products = $this->filtered($request)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'filters' => $request->only(['q', 'collection', 'stock']),
            'sort' => $sort,
            'direction' => $direction,
            'collections' => Product::distinct()->orderBy('collection')->pluck('collection'),
            'stats' => [
                'total' => Product::count(),
                'featured' => Product::where('is_featured', true)->count(),
                'lowStock' => Product::lowStock()->where('stock', '>', 0)->count(),
                'outOfStock' => Product::where('stock', 0)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['low_stock_threshold' => 5, 'specification' => [['engine' => '', 'fuelType' => '', 'year' => '']]]),
            'options' => $this->options(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->productAttributes());

        if ($product->stock > 0) {
            StockMovement::create([
                'bmuk_no' => $product->bmuk_no,
                'change' => $product->stock,
                'stock_after' => $product->stock,
                'reason' => StockMovement::REASON_RESTOCK,
                'note' => 'Opening stock',
                'user_id' => $request->user()->id,
            ]);
        }

        return redirect()->route('admin.products.index')->with('toast', ['type' => 'success', 'message' => "Product {$product->bmuk_no} added"]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'options' => $this->options(),
            'movements' => $product->stockMovements()->with('user:id,name')->latest()->limit(10)->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        // Stock changes go through adjustStock so every change is on the ledger.
        $product->update($request->productAttributes(includeStock: false));

        return redirect()->route('admin.products.index')->with('toast', ['type' => 'success', 'message' => "Product {$product->bmuk_no} updated"]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Soft delete: past orders keep pointing at the product.
        $product->delete();

        return back()->with('toast', ['type' => 'success', 'message' => "Product {$product->bmuk_no} removed from the shop"]);
    }

    public function adjustStock(Request $request, Product $product, AdjustStock $adjustStock): RedirectResponse
    {
        $data = $request->validate([
            'change' => ['required', 'integer', 'not_in:0', 'between:-10000,10000'],
            'reason' => ['required', Rule::in([StockMovement::REASON_RESTOCK, StockMovement::REASON_ADJUSTMENT])],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = $adjustStock->handle($product, (int) $data['change'], $data['reason'], $data['note'] ?? null, $request->user());

        return back()->with('toast', ['type' => 'success', 'message' => "Stock for {$product->bmuk_no} is now {$product->stock}"]);
    }

    public function export(Request $request): StreamedResponse
    {
        $products = $this->filtered($request)->orderBy('bmuk_no');

        return Csv::download('products-'.now()->format('Y-m-d').'.csv', [
            'BMUK no', 'Euro ID', 'OEM ID', 'Collection', 'Filter type', 'Brand', 'Model', 'Fits', 'Price', 'Stock', 'Low stock at', 'Featured',
        ], function () use ($products) {
            foreach ($products->lazy(200) as $product) {
                yield [
                    $product->bmuk_no, $product->euro_id, $product->oem_id, $product->collection, $product->filter_type,
                    $product->car_brand, $product->model, implode(' | ', $product->fitments), $product->price,
                    $product->stock, $product->low_stock_threshold, $product->is_featured ? 'yes' : 'no',
                ];
            }
        });
    }

    private function filtered(Request $request): Builder
    {
        return Product::query()
            ->search($request->input('q'))
            ->when($request->input('collection'), fn (Builder $query, string $collection) => $query->where('collection', $collection))
            ->when($request->input('stock') === 'low', fn (Builder $query) => $query->lowStock()->where('stock', '>', 0))
            ->when($request->input('stock') === 'out', fn (Builder $query) => $query->where('stock', 0))
            ->when($request->input('stock') === 'featured', fn (Builder $query) => $query->where('is_featured', true));
    }

    /**
     * Existing values offered as suggestions in the form.
     *
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'collections' => Product::withTrashed()->distinct()->orderBy('collection')->pluck('collection'),
            'filterTypes' => Product::withTrashed()->distinct()->orderBy('filter_type')->pluck('filter_type'),
            'brands' => Product::withTrashed()->distinct()->orderBy('car_brand')->pluck('car_brand'),
            'models' => Product::withTrashed()->distinct()->orderBy('model')->pluck('model'),
        ];
    }
}
