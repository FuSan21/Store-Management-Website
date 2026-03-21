<x-layouts.admin title="Customers">
    <x-slot:actions>
        <a href="{{ route('admin.customers.export', request()->query()) }}" class="btn btn-secondary"><x-icon name="download" :size="16"/> Export CSV</a>
    </x-slot:actions>

    <dl class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-3">
        <x-admin.stat label="Customers" :value="number_format($stats['total'])"/>
        <x-admin.stat label="Verified email" :value="number_format($stats['verified'])"/>
        <x-admin.stat label="Joined in 30 days" :value="number_format($stats['new'])" class="col-span-2 xl:col-span-1"/>
    </dl>

    <div class="card overflow-hidden">
        <form method="GET" class="flex flex-wrap gap-3 border-b border-line p-4">
            <input name="q" value="{{ $filters['q'] ?? '' }}" class="input min-w-56 flex-1" placeholder="Name or email" aria-label="Search customers">
            <select name="verified" class="input w-auto" aria-label="Verification">
                <option value="">Everyone</option>
                <option value="yes" @selected(($filters['verified'] ?? '') === 'yes')>Verified</option>
                <option value="no" @selected(($filters['verified'] ?? '') === 'no')>Not verified</option>
            </select>
            <button class="btn btn-primary">Filter</button>
            @if (array_filter($filters))<a href="{{ route('admin.customers.index') }}" class="btn btn-ghost">Reset</a>@endif
        </form>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Customer</th><th>Joined</th><th>Email</th><th class="text-right">Orders</th><th class="text-right">Spent</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-medium hover:underline">{{ $customer->name }}</a>
                                <span class="block text-xs text-muted">{{ $customer->email }}</span>
                            </td>
                            <td class="whitespace-nowrap text-muted">{{ $customer->created_at?->format('j M Y') }}</td>
                            <td>
                                @if ($customer->email_verified_at)
                                    <span class="inline-flex items-center gap-1 text-sm text-ok"><x-icon name="check" :size="14"/> Verified</span>
                                @else
                                    <span class="text-sm text-muted">Not verified</span>
                                @endif
                            </td>
                            <td class="text-right tabular-nums">{{ $customer->orders_count }}</td>
                            <td class="text-right font-medium tabular-nums">{{ money($customer->spent ?? 0) }}</td>
                            <td class="text-right">
                                @unless ($customer->email_verified_at)
                                    <form method="POST" action="{{ route('admin.customers.verify', $customer) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-secondary btn-sm">Mark verified</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-muted">No customers match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $customers->links() }}</div>
    </div>
</x-layouts.admin>
