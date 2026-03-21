@props(['title', 'heading' => null])
@php
    $links = [
        ['route' => 'account.dashboard', 'match' => 'account.dashboard', 'label' => 'Overview', 'icon' => 'dashboard'],
        ['route' => 'account.orders.index', 'match' => 'account.orders.*', 'label' => 'Orders', 'icon' => 'package'],
        ['route' => 'account.wishlist', 'match' => 'account.wishlist', 'label' => 'Wishlist', 'icon' => 'heart'],
        ['route' => 'account.addresses.index', 'match' => 'account.addresses.*', 'label' => 'Addresses', 'icon' => 'map-pin'],
        ['route' => 'account.profile.edit', 'match' => 'account.profile.*', 'label' => 'Profile & security', 'icon' => 'settings'],
    ];
@endphp
<x-layouts.store :title="$title">
    <div class="wrap py-10">
        <div class="grid gap-8 lg:grid-cols-[220px_1fr] lg:gap-12">
            <nav aria-label="Account" class="-mx-5 overflow-x-auto px-5 lg:mx-0 lg:px-0">
                <p class="mb-4 hidden text-sm text-muted lg:block">Signed in as<br><span class="font-medium text-ink">{{ auth()->user()->name }}</span></p>
                <ul class="flex gap-1 lg:flex-col">
                    @foreach ($links as $link)
                        @php($active = request()->routeIs($link['match']))
                        <li>
                            <a href="{{ route($link['route']) }}" @class([
                                'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium whitespace-nowrap transition-colors',
                                'bg-surface text-ink' => $active,
                                'text-muted hover:text-ink' => ! $active,
                            ]) @if ($active) aria-current="page" @endif>
                                <x-icon :name="$link['icon']" :size="16"/> {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="min-w-0">
                @if ($heading ?? $title)
                    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                        <h1 class="text-3xl font-semibold">{{ $heading ?? $title }}</h1>
                        {{ $actions ?? '' }}
                    </div>
                @endif
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.store>
