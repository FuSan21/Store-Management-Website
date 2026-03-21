<x-layouts.admin :title="$customer->name">
    <x-slot:subtitle>
        <p class="mt-1 text-sm text-muted">{{ $customer->email }} · joined {{ $customer->created_at?->format('j F Y') }}</p>
    </x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-ghost"><x-icon name="chevron-left" :size="16"/> Customers</a>
    </x-slot:actions>

    <dl class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-3">
        <x-admin.stat label="Orders" :value="number_format($orders->total())"/>
        <x-admin.stat label="Spent" :value="money($spent)"/>
        <x-admin.stat label="Email" :value="$customer->email_verified_at ? 'Verified' : 'Not verified'" class="col-span-2 xl:col-span-1"/>
    </dl>

    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <section class="card overflow-hidden" aria-labelledby="orders-title">
            <h2 id="orders-title" class="p-5 pb-3 font-semibold">Orders</h2>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>Order</th><th>Placed</th><th>Status</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="font-medium tabular-nums hover:underline">{{ $order->number() }}</a></td>
                                <td class="text-muted">{{ $order->created_at->format('j M Y') }}</td>
                                <td><x-status-badge :status="$order->status"/></td>
                                <td class="text-right font-medium tabular-nums">{{ money($order->total_cost) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-muted">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line p-4">{{ $orders->links() }}</div>
        </section>

        <aside class="space-y-6">
            <section class="card p-5 text-sm">
                <h2 class="font-semibold">Addresses</h2>
                @forelse ($customer->addresses as $address)
                    <p class="mt-3"><span class="font-medium">{{ $address->label }}</span><br><span class="text-muted">{{ $address->oneLine() }}@if ($address->phone)<br>{{ $address->phone }}@endif</span></p>
                @empty
                    <p class="mt-2 text-muted">No saved addresses.</p>
                @endforelse
            </section>
            <section class="card p-5 text-sm">
                <h2 class="font-semibold">Recent reviews</h2>
                @forelse ($reviews as $review)
                    <div class="mt-3">
                        <x-stars :rating="$review->rating"/>
                        <p class="mt-1">{{ Str::limit($review->comment, 120) }}</p>
                        <p class="text-xs text-muted">{{ $review->product?->title }}</p>
                    </div>
                @empty
                    <p class="mt-2 text-muted">No reviews yet.</p>
                @endforelse
            </section>
        </aside>
    </div>
</x-layouts.admin>
