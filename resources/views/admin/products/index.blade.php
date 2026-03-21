@php
    $sortLink = fn (string $column) => route('admin.products.index', [...request()->query(), 'sort' => $column, 'dir' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc']);
    $arrow = fn (string $column) => $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<x-layouts.admin title="Products">
    <x-slot:actions>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.export', request()->query()) }}" class="btn btn-secondary"><x-icon name="download" :size="16"/> Export CSV</a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><x-icon name="plus" :size="16"/> Add product</a>
        </div>
    </x-slot:actions>

    <dl class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-admin.stat label="Products" :value="number_format($stats['total'])"/>
        <x-admin.stat label="Featured" :value="number_format($stats['featured'])"/>
        <x-admin.stat label="Running low" :value="number_format($stats['lowStock'])"/>
        <x-admin.stat label="Out of stock" :value="number_format($stats['outOfStock'])"/>
    </dl>

    <div class="card overflow-hidden">
        <form method="GET" class="flex flex-wrap gap-3 border-b border-line p-4">
            <input name="q" value="{{ $filters['q'] ?? '' }}" class="input min-w-56 flex-1" placeholder="Search BMUK, OEM, brand or model" aria-label="Search products">
            <select name="collection" class="input w-auto" aria-label="Collection">
                <option value="">All parts</option>
                @foreach ($collections as $option)
                    <option value="{{ $option }}" @selected(($filters['collection'] ?? '') === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <select name="stock" class="input w-auto" aria-label="Stock">
                <option value="">Any stock</option>
                <option value="low" @selected(($filters['stock'] ?? '') === 'low')>Running low</option>
                <option value="out" @selected(($filters['stock'] ?? '') === 'out')>Out of stock</option>
                <option value="featured" @selected(($filters['stock'] ?? '') === 'featured')>Featured</option>
            </select>
            <button class="btn btn-primary">Filter</button>
            @if (array_filter($filters))<a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Reset</a>@endif
        </form>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th><a href="{{ $sortLink('bmuk_no') }}" class="hover:text-ink">BMUK {{ $arrow('bmuk_no') }}</a></th>
                        <th>Product</th>
                        <th><a href="{{ $sortLink('car_brand') }}" class="hover:text-ink">Vehicle {{ $arrow('car_brand') }}</a></th>
                        <th class="text-right"><a href="{{ $sortLink('price') }}" class="hover:text-ink">Price {{ $arrow('price') }}</a></th>
                        <th class="text-right"><a href="{{ $sortLink('stock') }}" class="hover:text-ink">Stock {{ $arrow('stock') }}</a></th>
                        <th>Rating</th>
                        <th class="text-right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="font-medium tabular-nums">{{ $product->bmuk_no }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->thumb_url }}" alt="" class="size-10 rounded-lg bg-surface object-contain p-1">
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="font-medium hover:underline">{{ $product->collection }}</a>
                                        @if ($product->is_featured)<span class="ml-1 rounded-full bg-accent-soft px-1.5 py-0.5 text-[10px] font-semibold text-accent uppercase">Featured</span>@endif
                                        <span class="block text-xs text-muted">{{ $product->filter_type }} · OEM {{ $product->oem_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->car_brand }} {{ $product->model }}</td>
                            <td class="text-right tabular-nums">{{ money($product->price) }}</td>
                            <td class="text-right">
                                <span @class([
                                    'rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums',
                                    'bg-accent-soft text-accent' => $product->stock === 0,
                                    'bg-warn-soft text-warn' => $product->is_low_stock,
                                    'bg-surface' => $product->stock > $product->low_stock_threshold,
                                ])>{{ $product->stock }}</span>
                            </td>
                            <td>
                                @if ($product->reviews_count)
                                    <span class="inline-flex items-center gap-1 text-sm"><x-icon name="star" :size="14"/> {{ number_format($product->reviews_avg_rating, 1) }} <span class="text-muted">({{ $product->reviews_count }})</span></span>
                                @else
                                    <span class="text-sm text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('products.show', $product) }}" class="btn-icon size-8" title="View in shop" aria-label="View {{ $product->bmuk_no }} in shop"><x-icon name="external" :size="16"/></a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon size-8" title="Edit" aria-label="Edit {{ $product->bmuk_no }}"><x-icon name="pencil" :size="16"/></a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" x-data x-confirm="Remove product {{ $product->bmuk_no }} from the shop? Past orders keep their record.">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon size-8 text-muted hover:text-accent" title="Remove" aria-label="Remove {{ $product->bmuk_no }}"><x-icon name="trash" :size="16"/></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-muted">No products match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $products->links() }}</div>
    </div>
</x-layouts.admin>
