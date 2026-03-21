<x-layouts.account title="My account" :heading="'Hello, '.Str::before(auth()->user()->name, ' ')">
    @unless (auth()->user()->hasVerifiedEmail())
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-line bg-warn-soft p-4 text-sm text-warn">
            <span class="flex items-center gap-2"><x-icon name="mail" :size="18"/> Confirm your email so we can send order updates.</span>
            <form method="POST" action="{{ route('verification.send') }}">@csrf<button class="link text-warn">Resend the link</button></form>
        </div>
    @endunless

    <dl class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <div class="card p-5"><dt class="text-sm text-muted">Orders</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $stats['orders'] }}</dd></div>
        <div class="card p-5"><dt class="text-sm text-muted">In progress</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $stats['open'] }}</dd></div>
        <div class="card p-5"><dt class="text-sm text-muted">Spent</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ money($stats['spent']) }}</dd></div>
        <div class="card p-5"><dt class="text-sm text-muted">Saved items</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $stats['wishlist'] }}</dd></div>
    </dl>

    @if ($toReview->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-lg font-semibold">How did these fit?</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-3">
                @foreach ($toReview as $item)
                    <div class="card flex items-center gap-3 p-3">
                        <img src="{{ $item['product']->thumb_url }}" alt="" class="size-14 rounded-lg bg-surface object-contain p-1.5">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $item['product']->collection }}</p>
                            <a href="{{ route('account.reviews.create', [$item['order'], $item['product']]) }}" class="link text-sm text-accent">Write a review</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-10">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Recent orders</h2>
            <a href="{{ route('account.orders.index') }}" class="link text-sm">All orders</a>
        </div>
        @include('account.orders._list', ['orders' => $recentOrders])
    </section>
</x-layouts.account>
