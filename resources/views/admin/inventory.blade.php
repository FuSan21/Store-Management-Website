@php
    $reasons = ['sale' => 'Sales', 'restock' => 'Deliveries', 'cancellation' => 'Cancellations', 'adjustment' => 'Corrections'];
@endphp
<x-layouts.admin title="Inventory">
    <dl class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-3">
        <x-admin.stat label="Units on the shelf" :value="number_format($units)"/>
        <x-admin.stat label="Stock value at retail" :value="money($stockValue)"/>
        <x-admin.stat label="Need reordering" :value="number_format($lowStock->count())" class="col-span-2 xl:col-span-1"/>
    </dl>

    <div class="grid gap-6 xl:grid-cols-[1fr_1.3fr]">
        <section class="card overflow-hidden" aria-labelledby="reorder-title">
            <div class="p-5 pb-3">
                <h2 id="reorder-title" class="font-semibold">Reorder list</h2>
                <p class="text-sm text-muted">At or below each product's warning level</p>
            </div>
            @if ($lowStock->isEmpty())
                <p class="px-5 pb-6 text-sm text-muted">Nothing to reorder.</p>
            @else
                <ul class="divide-y divide-line border-t border-line">
                    @foreach ($lowStock as $product)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <img src="{{ $product->thumb_url }}" alt="" class="size-10 rounded-lg bg-surface object-contain p-1">
                            <div class="min-w-0 flex-1 text-sm">
                                <a href="{{ route('admin.products.edit', $product) }}" class="block truncate font-medium hover:underline">{{ $product->title }}</a>
                                <span class="text-muted">BMUK {{ $product->bmuk_no }} · warn at {{ $product->low_stock_threshold }}</span>
                            </div>
                            <span @class(['rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums', 'bg-accent-soft text-accent' => $product->stock === 0, 'bg-warn-soft text-warn' => $product->stock > 0])>
                                {{ $product->stock === 0 ? 'Out' : $product->stock }}
                            </span>
                            <a href="{{ route('admin.products.edit', $product) }}#f-change" class="btn btn-secondary btn-sm">Restock</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="card overflow-hidden" aria-labelledby="ledger-title">
            <div class="flex flex-wrap items-center justify-between gap-3 p-5 pb-3">
                <h2 id="ledger-title" class="font-semibold">Stock ledger</h2>
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ route('admin.inventory') }}" @class(['rounded-full border px-3 py-1 text-xs', 'border-ink bg-ink text-bg' => ! $reason, 'border-line hover:border-ink' => $reason])>All</a>
                    @foreach ($reasons as $key => $label)
                        <a href="{{ route('admin.inventory', ['reason' => $key]) }}" @class(['rounded-full border px-3 py-1 text-xs', 'border-ink bg-ink text-bg' => $reason === $key, 'border-line hover:border-ink' => $reason !== $key])>{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>When</th><th>Product</th><th>Reason</th><th class="text-right">Change</th><th class="text-right">After</th></tr></thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="whitespace-nowrap text-muted">{{ $movement->created_at->format('j M, H:i') }}</td>
                                <td>
                                    <span class="font-medium">{{ $movement->bmuk_no }}</span>
                                    <span class="block text-xs text-muted">{{ $movement->product?->collection }}</span>
                                </td>
                                <td>
                                    <span class="capitalize">{{ $movement->reason }}</span>
                                    @if ($movement->order)
                                        <a href="{{ route('admin.orders.show', $movement->order) }}" class="block text-xs text-muted hover:underline">{{ $movement->order->number() }}</a>
                                    @elseif ($movement->note)
                                        <span class="block text-xs text-muted">{{ $movement->note }}</span>
                                    @endif
                                </td>
                                <td @class(['text-right font-semibold tabular-nums', 'text-ok' => $movement->change > 0, 'text-accent' => $movement->change < 0])>{{ $movement->change > 0 ? '+' : '' }}{{ $movement->change }}</td>
                                <td class="text-right tabular-nums">{{ $movement->stock_after }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-muted">No stock movements yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line p-4">{{ $movements->links() }}</div>
        </section>
    </div>
</x-layouts.admin>
