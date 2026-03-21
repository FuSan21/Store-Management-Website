<x-layouts.admin :title="'Order '.$order->number()">
    <x-slot:subtitle>
        <p class="mt-1 flex items-center gap-3 text-sm text-muted">Placed {{ $order->created_at->format('j F Y, H:i') }} <x-status-badge :status="$order->status"/></p>
    </x-slot:subtitle>
    <x-slot:actions>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost"><x-icon name="chevron-left" :size="16"/> Orders</a>
            <a href="{{ route('account.orders.invoice', $order) }}" class="btn btn-secondary"><x-icon name="file" :size="16"/> Invoice PDF</a>
        </div>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <section class="card overflow-hidden" aria-label="Items">
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead><tr><th>Item</th><th class="text-right">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th></tr></thead>
                        <tbody>
                            @foreach ($order->orderDetails as $line)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $line->product?->thumb_url }}" alt="" class="size-11 rounded-lg bg-surface object-contain p-1">
                                            <div>
                                                <p class="font-medium">{{ $line->product?->title ?? 'Deleted product' }}</p>
                                                <p class="text-xs text-muted">BMUK {{ $line->bmuk_no }} · {{ $line->product?->stock ?? 0 }} in stock now</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right tabular-nums">{{ $line->quantity }}</td>
                                    <td class="text-right tabular-nums">{{ money($line->price()) }}</td>
                                    <td class="text-right font-medium tabular-nums">{{ money($line->lineTotal()) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <dl class="ml-auto max-w-xs space-y-2 p-5 text-sm">
                    <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="tabular-nums">{{ money($order->subtotal) }}</dd></div>
                    @if ((float) $order->discount > 0)
                        <div class="flex justify-between"><dt class="text-muted">Discount ({{ $order->coupon_code }})</dt><dd class="text-ok tabular-nums">−{{ money($order->discount) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-muted">Delivery</dt><dd class="tabular-nums">{{ money($order->shipping_cost) }}</dd></div>
                    <div class="flex justify-between border-t border-line pt-2 text-base font-semibold"><dt>Total</dt><dd class="tabular-nums">{{ money($order->total_cost) }}</dd></div>
                </dl>
            </section>

            <section class="card p-5" aria-labelledby="history-title">
                <h2 id="history-title" class="font-semibold">History</h2>
                <ol class="mt-4 space-y-4 border-l-2 border-line pl-5">
                    @foreach ($order->statusHistories->reverse() as $entry)
                        <li class="relative">
                            <span class="absolute top-1 -left-[27px] size-3 rounded-full border-2 border-bg bg-ink"></span>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$entry->status"/>
                                <span class="text-xs text-muted">{{ $entry->created_at->format('j M Y, H:i') }} · {{ $entry->changedBy?->name ?? 'System' }}</span>
                            </div>
                            @if ($entry->note)<p class="mt-1.5 text-sm">{{ $entry->note }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card p-5" aria-labelledby="status-title">
                <h2 id="status-title" class="font-semibold">Update status</h2>
                @if ($order->status->transitions())
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 space-y-4">
                        @csrf @method('PATCH')
                        <x-field label="Move to" name="status" type="select">
                            @foreach ($order->status->transitions() as $next)
                                <option value="{{ $next->value }}">{{ $next->label() }}</option>
                            @endforeach
                        </x-field>
                        <x-field label="Note to the customer (optional)" name="note" type="textarea" rows="2" placeholder="Steadfast tracking SF123456"/>
                        <p class="text-xs text-muted">The customer is emailed. Cancelling puts the items back in stock.</p>
                        <button class="btn btn-primary w-full">Update order</button>
                    </form>
                @else
                    <p class="mt-2 text-sm text-muted">This order is {{ strtolower($order->status->label()) }} and cannot change.</p>
                @endif
            </section>

            <section class="card p-5 text-sm">
                <h2 class="font-semibold">Customer</h2>
                @if ($order->user)
                    <p class="mt-3"><a href="{{ route('admin.customers.show', $order->user) }}" class="link">{{ $order->user->name }}</a></p>
                    <p class="text-muted">{{ $order->user->email }}</p>
                @else
                    <p class="mt-3 text-muted">Account deleted</p>
                @endif
                @if ($order->phone)<p class="mt-1 text-muted">{{ $order->phone }}</p>@endif
            </section>

            <section class="card space-y-4 p-5 text-sm">
                <div>
                    <h2 class="font-semibold">Delivery</h2>
                    <p class="mt-2 text-muted">{{ $order->shipping_address }}</p>
                    <p class="mt-1">{{ $order->shipping_method->label() }}</p>
                </div>
                <div>
                    <h2 class="font-semibold">Payment</h2>
                    <p class="mt-2">{{ $order->payment_method->label() }}</p>
                    <p class="mt-1 text-muted">Billing: {{ $order->billing_address }}</p>
                </div>
                @if ($order->notes)
                    <div>
                        <h2 class="font-semibold">Customer note</h2>
                        <p class="mt-2 text-muted">{{ $order->notes }}</p>
                    </div>
                @endif
            </section>
        </aside>
    </div>
</x-layouts.admin>
