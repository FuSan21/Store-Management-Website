<x-layouts.admin title="Orders">
    <x-slot:actions>
        <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-secondary"><x-icon name="download" :size="16"/> Export CSV</a>
    </x-slot:actions>

    <div class="-mx-5 mb-5 flex gap-2 overflow-x-auto px-5 sm:mx-0 sm:px-0">
        <a href="{{ route('admin.orders.index', array_filter(['q' => $filters['q'] ?? null])) }}" @class(['rounded-full border px-3.5 py-1.5 text-sm whitespace-nowrap', 'border-ink bg-ink text-bg' => empty($filters['status']), 'border-line bg-bg hover:border-ink' => ! empty($filters['status'])])>
            All <span class="tabular-nums opacity-70">{{ $counts->sum() }}</span>
        </a>
        @foreach (\App\Enums\OrderStatus::cases() as $case)
            @php($on = ($filters['status'] ?? null) === $case->value)
            <a href="{{ route('admin.orders.index', array_filter(['status' => $case->value, 'q' => $filters['q'] ?? null])) }}" @class(['rounded-full border px-3.5 py-1.5 text-sm whitespace-nowrap', 'border-ink bg-ink text-bg' => $on, 'border-line bg-bg hover:border-ink' => ! $on])>
                {{ $case->label() }} <span class="tabular-nums opacity-70">{{ $counts[$case->value] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="flex flex-wrap items-end gap-3 border-b border-line p-4">
            @if (! empty($filters['status']))<input type="hidden" name="status" value="{{ $filters['status'] }}">@endif
            <div class="min-w-56 flex-1">
                <label for="q" class="sr-only">Search orders</label>
                <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="Order number, customer, email or phone">
            </div>
            <div>
                <label for="from" class="mb-1 block text-xs text-muted">From</label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input">
            </div>
            <div>
                <label for="to" class="mb-1 block text-xs text-muted">To</label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input">
            </div>
            <button class="btn btn-primary">Filter</button>
            @if (array_filter($filters))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Order</th><th>Placed</th><th>Customer</th><th>Status</th><th>Payment</th><th class="text-right">Items</th><th class="text-right">Total</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="font-medium tabular-nums hover:underline">{{ $order->number() }}</a></td>
                            <td class="whitespace-nowrap text-muted">{{ $order->created_at->format('j M Y, H:i') }}</td>
                            <td>
                                @if ($order->user)
                                    <a href="{{ route('admin.customers.show', $order->user) }}" class="hover:underline">{{ $order->user->name }}</a>
                                    <span class="block text-xs text-muted">{{ $order->user->email }}</span>
                                @else
                                    <span class="text-muted">Deleted account</span>
                                @endif
                            </td>
                            <td><x-status-badge :status="$order->status"/></td>
                            <td class="text-muted">{{ $order->payment_method->label() }}</td>
                            <td class="text-right tabular-nums">{{ $order->itemCount() }}</td>
                            <td class="text-right font-medium tabular-nums">{{ money($order->total_cost) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-muted">No orders match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $orders->links() }}</div>
    </div>
</x-layouts.admin>
