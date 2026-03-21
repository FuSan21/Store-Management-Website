<x-layouts.account :title="'Order '.$order->number()">
    <x-slot:actions>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('account.orders.invoice', $order) }}" class="btn btn-secondary"><x-icon name="download" :size="16"/> Invoice PDF</a>
            @can('cancel', $order)
                <form method="POST" action="{{ route('account.orders.cancel', $order) }}" x-data x-confirm="Cancel order {{ $order->number() }}? This cannot be undone.">
                    @csrf
                    <button class="btn btn-danger">Cancel order</button>
                </form>
            @endcan
        </div>
    </x-slot:actions>

    <div class="card p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-muted">Placed {{ $order->created_at->format('j F Y, g:i a') }}</p>
            <x-status-badge :status="$order->status"/>
        </div>
        <x-order-timeline :order="$order"/>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
        <section class="card overflow-hidden" aria-label="Items">
            <ul class="divide-y divide-line">
                @foreach ($order->orderDetails as $line)
                    <li class="flex flex-wrap items-center gap-4 p-4">
                        <img src="{{ $line->product?->thumb_url }}" alt="" class="size-16 rounded-lg bg-surface object-contain p-2">
                        <div class="min-w-0 flex-1">
                            @if ($line->product && ! $line->product->trashed())
                                <a href="{{ route('products.show', $line->product) }}" class="font-medium hover:underline">{{ $line->product->title }}</a>
                            @else
                                <span class="font-medium">{{ $line->product?->title ?? 'Discontinued product' }}</span>
                            @endif
                            <p class="text-sm text-muted">{{ $line->quantity }} × {{ money($line->price()) }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            @can('review', $order)
                                @if ($line->product && ! in_array($line->product->bmuk_no, $reviewed))
                                    <a href="{{ route('account.reviews.create', [$order, $line->product]) }}" class="btn btn-secondary btn-sm"><x-icon name="star" :size="14"/> Review</a>
                                @elseif ($line->product)
                                    <span class="inline-flex items-center gap-1 text-xs text-ok"><x-icon name="check" :size="14"/> Reviewed</span>
                                @endif
                            @endcan
                            <span class="font-semibold tabular-nums">{{ money($line->lineTotal()) }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <dl class="space-y-2 border-t border-line bg-surface/50 p-4 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                @if ((float) $order->discount > 0)
                    <div class="flex justify-between"><dt class="text-muted">Discount {{ $order->coupon_code ? "({$order->coupon_code})" : '' }}</dt><dd class="text-ok">−{{ money($order->discount) }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-muted">Delivery</dt><dd>{{ (float) $order->shipping_cost > 0 ? money($order->shipping_cost) : 'Free' }}</dd></div>
                <div class="flex justify-between border-t border-line pt-2 text-base font-semibold"><dt>Total</dt><dd>{{ money($order->total_cost) }}</dd></div>
            </dl>
        </section>

        <aside class="space-y-6">
            <div class="card p-5 text-sm">
                <h2 class="font-semibold">Delivery</h2>
                <p class="mt-2 text-muted">{{ $order->shipping_address }}</p>
                @if ($order->phone)<p class="mt-1 text-muted">{{ $order->phone }}</p>@endif
                <p class="mt-3">{{ $order->shipping_method->label() }} <span class="text-muted">· {{ $order->shipping_method->eta() }}</span></p>
                @if ($order->notes)<p class="mt-3 text-muted">“{{ $order->notes }}”</p>@endif
            </div>
            <div class="card p-5 text-sm">
                <h2 class="font-semibold">Payment</h2>
                <p class="mt-2">{{ $order->payment_method->label() }}</p>
                <p class="mt-1 text-muted">Billing: {{ $order->billing_address }}</p>
            </div>
        </aside>
    </div>
</x-layouts.account>
