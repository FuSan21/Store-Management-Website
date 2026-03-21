@props(['title', 'heading' => null])
@php
    $lowStock = \App\Models\Product::lowStock()->count();
    $waiting = \App\Models\Order::where('status', \App\Enums\OrderStatus::WaitingForPayment->value)->count();
    $links = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'label' => 'Orders', 'icon' => 'package', 'count' => $waiting],
        ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'label' => 'Products', 'icon' => 'box'],
        ['route' => 'admin.inventory', 'match' => 'admin.inventory', 'label' => 'Inventory', 'icon' => 'warehouse', 'count' => $lowStock, 'alert' => true],
        ['route' => 'admin.customers.index', 'match' => 'admin.customers.*', 'label' => 'Customers', 'icon' => 'users'],
        ['route' => 'admin.coupons.index', 'match' => 'admin.coupons.*', 'label' => 'Coupons', 'icon' => 'ticket'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title.' · Admin', 'description' => null])
</head>
<body class="min-h-dvh bg-surface text-ink" x-data="{ nav: false }" @keydown.escape="nav = false">
    {{-- Sidebar --}}
    <aside :class="nav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-line bg-bg transition-transform duration-200">
        <div class="flex h-16 items-center justify-between px-5">
            <a href="{{ route('admin.dashboard') }}"><x-logo/></a>
            <button type="button" class="btn-icon lg:hidden" @click="nav = false" aria-label="Close menu"><x-icon name="x"/></button>
        </div>
        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4" aria-label="Admin">
            @foreach ($links as $link)
                @php($active = request()->routeIs($link['match']))
                <a href="{{ route($link['route']) }}" @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                    'bg-surface text-ink' => $active,
                    'text-muted hover:bg-surface hover:text-ink' => ! $active,
                ]) @if ($active) aria-current="page" @endif>
                    <x-icon :name="$link['icon']" :size="18"/>
                    <span class="flex-1">{{ $link['label'] }}</span>
                    @if (! empty($link['count']))
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums',
                            'bg-warn-soft text-warn' => ! empty($link['alert']),
                            'bg-ink text-bg' => empty($link['alert']),
                        ])>{{ $link['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
        <div class="space-y-1 border-t border-line p-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-muted hover:bg-surface hover:text-ink">
                <x-icon name="store" :size="18"/> View the shop
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-muted hover:bg-surface hover:text-ink">
                    <x-icon name="log-out" :size="18"/> Sign out
                </button>
            </form>
        </div>
    </aside>
    <div x-show="nav" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="nav = false"></div>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-line bg-bg/90 px-5 backdrop-blur sm:px-8">
            <button type="button" class="btn-icon -ml-2 lg:hidden" @click="nav = true" aria-label="Open menu"><x-icon name="menu"/></button>
            <p class="text-sm text-muted">{{ now()->format('l, j F Y') }}</p>
            <div class="ml-auto flex items-center gap-2">
                <x-theme-toggle/>
                <span class="hidden text-sm font-medium sm:inline">{{ auth()->user()->name }}</span>
            </div>
        </header>

        <main class="px-5 py-8 sm:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold sm:text-3xl">{{ $heading ?? $title }}</h1>
                        {{ $subtitle ?? '' }}
                    </div>
                    {{ $actions ?? '' }}
                </div>
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-toasts/>
    @vite('resources/js/admin.js')
</body>
</html>
