<x-layouts.account title="Orders">
    <div class="-mx-5 mb-2 flex gap-2 overflow-x-auto px-5 sm:mx-0 sm:px-0" role="tablist" aria-label="Filter by status">
        <a href="{{ route('account.orders.index') }}" @class(['rounded-full border px-3.5 py-1.5 text-sm whitespace-nowrap', 'border-ink bg-ink text-bg' => ! $status, 'border-line hover:border-ink' => $status])>All</a>
        @foreach (\App\Enums\OrderStatus::cases() as $case)
            <a href="{{ route('account.orders.index', ['status' => $case->value]) }}" @class(['rounded-full border px-3.5 py-1.5 text-sm whitespace-nowrap', 'border-ink bg-ink text-bg' => $status === $case, 'border-line hover:border-ink' => $status !== $case])>{{ $case->label() }}</a>
        @endforeach
    </div>

    @include('account.orders._list', ['orders' => $orders])

    <div class="mt-8">{{ $orders->links() }}</div>
</x-layouts.account>
