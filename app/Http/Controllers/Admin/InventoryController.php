<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $reason = $request->input('reason');

        return view('admin.inventory', [
            'lowStock' => Product::lowStock()->orderBy('stock')->orderBy('bmuk_no')->get(),
            'stockValue' => (float) Product::query()->selectRaw('sum(price * stock) as value')->value('value'),
            'units' => (int) Product::sum('stock'),
            'movements' => StockMovement::with(['product', 'user:id,name', 'order:id'])
                ->when($reason, fn ($query) => $query->where('reason', $reason))
                ->latest()
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'reason' => $reason,
        ]);
    }
}
