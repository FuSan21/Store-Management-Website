@php
    $totalRevenue = array_sum($revenueSeries['revenue']);
    $statusMax = max(1, collect($statusBreakdown)->max('count'));
    $topMax = max(1, $topProducts->max('revenue') ?? 1);
@endphp
<x-layouts.admin title="Dashboard">
    <x-slot:actions>
        <div class="inline-flex rounded-lg border border-line bg-bg p-1" role="tablist" aria-label="Time range">
            @foreach ($ranges as $range)
                <a href="{{ route('admin.dashboard', ['days' => $range]) }}" role="tab" aria-selected="{{ $days === $range ? 'true' : 'false' }}"
                    @class(['rounded-md px-3 py-1.5 text-sm font-medium transition-colors', 'bg-ink text-bg' => $days === $range, 'text-muted hover:text-ink' => $days !== $range])>
                    {{ $range }} days
                </a>
            @endforeach
        </div>
    </x-slot:actions>

    <dl class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-admin.stat label="Revenue" :value="money($kpis['revenue']['value'])" :delta="$kpis['revenue']['delta']"/>
        <x-admin.stat label="Paid orders" :value="number_format($kpis['orders']['value'])" :delta="$kpis['orders']['delta']"/>
        <x-admin.stat label="Average order" :value="money($kpis['average']['value'])" :delta="$kpis['average']['delta']"/>
        <x-admin.stat label="New customers" :value="number_format($kpis['customers']['value'])" :delta="$kpis['customers']['delta']"/>
    </dl>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="card p-5 xl:col-span-2" aria-labelledby="revenue-title">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 id="revenue-title" class="font-semibold">Revenue per day</h2>
                <p class="text-sm text-muted">{{ money($totalRevenue) }} over {{ $days }} days · paid, shipped and completed orders</p>
            </div>
            <div class="mt-4 h-72">
                <canvas data-chart="revenue" data-series='@json($revenueSeries)' data-currency="{{ config('store.currency') }}"
                    role="img" aria-label="Line chart of revenue per day over the last {{ $days }} days, totalling {{ money($totalRevenue) }}"></canvas>
            </div>
            <details class="mt-3 text-sm">
                <summary class="cursor-pointer text-muted hover:text-ink">Show as a table</summary>
                <div class="mt-3 max-h-64 overflow-y-auto rounded-lg border border-line">
                    <table class="table-base">
                        <thead><tr><th>Day</th><th class="text-right">Orders</th><th class="text-right">Revenue</th></tr></thead>
                        <tbody>
                            @foreach ($revenueSeries['labels'] as $i => $label)
                                <tr><td>{{ $label }}</td><td class="text-right tabular-nums">{{ $revenueSeries['orders'][$i] }}</td><td class="text-right tabular-nums">{{ money($revenueSeries['revenue'][$i]) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </section>

        <section class="card p-5" aria-labelledby="status-title">
            <h2 id="status-title" class="font-semibold">Orders by status</h2>
            <p class="text-sm text-muted">Placed in the last {{ $days }} days</p>
            <ul class="mt-5 space-y-3.5">
                @foreach ($statusBreakdown as $row)
                    <li>
                        <a href="{{ route('admin.orders.index', ['status' => $row['status']->value]) }}" class="group block">
                            <div class="flex items-center justify-between text-sm">
                                <span class="group-hover:underline">{{ $row['status']->label() }}</span>
                                <span class="font-semibold tabular-nums">{{ $row['count'] }}</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-surface">
                                <div class="h-full rounded-full bg-ink" style="width: {{ $row['count'] / $statusMax * 100 }}%"></div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="card overflow-hidden xl:col-span-2" aria-labelledby="action-title">
            <div class="flex items-center justify-between p-5 pb-3">
                <h2 id="action-title" class="font-semibold">Orders waiting on you</h2>
                <a href="{{ route('admin.orders.index') }}" class="link text-sm">All orders</a>
            </div>
            @if ($needsAction->isEmpty())
                <p class="px-5 pb-6 text-sm text-muted">Nothing waiting — every order has moved on.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Waiting</th><th class="text-right">Total</th></tr></thead>
                        <tbody>
                            @foreach ($needsAction as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="font-medium hover:underline tabular-nums">{{ $order->number() }}</a></td>
                                    <td>{{ $order->user?->name ?? 'Deleted account' }}</td>
                                    <td><x-status-badge :status="$order->status"/></td>
                                    <td class="text-muted">{{ $order->created_at->diffForHumans(short: true, syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</td>
                                    <td class="text-right font-medium tabular-nums">{{ money($order->total_cost) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card p-5" aria-labelledby="stock-title">
            <div class="flex items-center justify-between">
                <h2 id="stock-title" class="font-semibold">Low stock</h2>
                <a href="{{ route('admin.inventory') }}" class="link text-sm">{{ $lowStockCount }} {{ Str::plural('product', $lowStockCount) }}</a>
            </div>
            @forelse ($lowStock as $product)
                <a href="{{ route('admin.products.edit', $product) }}" class="mt-3 flex items-center gap-3 rounded-lg p-1 -mx-1 hover:bg-surface">
                    <img src="{{ $product->thumb_url }}" alt="" class="size-10 rounded-lg bg-surface object-contain p-1">
                    <span class="min-w-0 flex-1 text-sm">
                        <span class="block truncate font-medium">{{ $product->title }}</span>
                        <span class="text-muted">BMUK {{ $product->bmuk_no }}</span>
                    </span>
                    <span @class(['rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums', 'bg-accent-soft text-accent' => $product->stock === 0, 'bg-warn-soft text-warn' => $product->stock > 0])>
                        {{ $product->stock === 0 ? 'Out' : $product->stock.' left' }}
                    </span>
                </a>
            @empty
                <p class="mt-4 text-sm text-muted">Every product is above its reorder level.</p>
            @endforelse
        </section>
    </div>

    <section class="card mt-6 overflow-hidden" aria-labelledby="top-title">
        <div class="flex items-center justify-between p-5 pb-3">
            <h2 id="top-title" class="font-semibold">Top products by revenue</h2>
            <span class="text-sm text-muted">Last {{ $days }} days</span>
        </div>
        @if ($topProducts->isEmpty())
            <p class="px-5 pb-6 text-sm text-muted">No paid orders in this period yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>Product</th><th class="text-right">Units</th><th class="w-1/3">Revenue</th></tr></thead>
                    <tbody>
                        @foreach ($topProducts as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.edit', $row['product']) }}" class="font-medium hover:underline">{{ $row['product']->title }}</a>
                                    <span class="block text-xs text-muted">BMUK {{ $row['product']->bmuk_no }}</span>
                                </td>
                                <td class="text-right tabular-nums">{{ $row['units'] }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-surface"><div class="h-full rounded-full bg-accent" style="width: {{ $row['revenue'] / $topMax * 100 }}%"></div></div>
                                        <span class="w-24 text-right font-medium tabular-nums">{{ money($row['revenue']) }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.admin>
