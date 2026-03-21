@if ($orders->isEmpty())
    <x-empty-state icon="package" title="No orders yet" body="When you place an order it shows up here with its delivery progress." class="mt-4">
        <a href="{{ route('shop') }}" class="btn btn-primary">Find a filter</a>
    </x-empty-state>
@else
    <ul class="mt-4 divide-y divide-line overflow-hidden rounded-xl border border-line">
        @foreach ($orders as $order)
            <li>
                <a href="{{ route('account.orders.show', $order) }}" class="flex flex-wrap items-center gap-x-6 gap-y-2 bg-raised px-5 py-4 transition-colors hover:bg-surface">
                    <span class="min-w-28">
                        <span class="block font-semibold tabular-nums">{{ $order->number() }}</span>
                        <span class="text-sm text-muted">{{ $order->created_at->format('j M Y') }}</span>
                    </span>
                    <x-status-badge :status="$order->status"/>
                    <span class="text-sm text-muted">{{ $order->itemCount() }} {{ Str::plural('item', $order->itemCount()) }}</span>
                    <span class="ml-auto font-semibold tabular-nums">{{ money($order->total_cost) }}</span>
                    <x-icon name="chevron-right" :size="18" class="text-muted"/>
                </a>
            </li>
        @endforeach
    </ul>
@endif
