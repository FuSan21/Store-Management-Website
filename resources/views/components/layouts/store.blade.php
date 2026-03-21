@props(['title' => null, 'description' => null])
@php
    $cartCount = app(\App\Support\Cart\CartService::class)->count();
    $nav = [
        ['label' => 'All filters', 'url' => route('shop'), 'active' => request()->routeIs('shop') && ! request('collection')],
        ['label' => 'Oil', 'url' => route('shop', ['collection' => 'Oil Filter']), 'active' => request('collection') === 'Oil Filter'],
        ['label' => 'Air', 'url' => route('shop', ['collection' => 'Air Filter']), 'active' => request('collection') === 'Air Filter'],
        ['label' => 'Fuel', 'url' => route('shop', ['collection' => 'Fuel Filter']), 'active' => request('collection') === 'Fuel Filter'],
        ['label' => 'Cabin', 'url' => route('shop', ['collection' => 'Cabin Filter']), 'active' => request('collection') === 'Cabin Filter'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title, 'description' => $description])
</head>
<body class="flex min-h-dvh flex-col bg-bg text-ink" x-data="{ menu: false }" @keydown.escape="menu = false">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[70] focus:rounded-lg focus:bg-ink focus:px-4 focus:py-2 focus:text-bg">Skip to content</a>

    <div class="bg-ink text-bg">
        <p class="wrap py-2 text-center text-xs sm:text-[13px]">
            Free delivery on orders over {{ money(config('store.free_shipping_threshold')) }} · Cash on delivery across Bangladesh
        </p>
    </div>

    <header class="sticky top-0 z-40 border-b border-line bg-bg/90 backdrop-blur supports-[backdrop-filter]:bg-bg/80">
        <div class="wrap flex h-16 items-center gap-4">
            <button type="button" class="btn-icon -ml-2 lg:hidden" @click="menu = true" aria-label="Open menu">
                <x-icon name="menu"/>
            </button>

            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ config('store.name') }} home">
                <span class="hidden sm:block"><x-logo/></span>
                <span class="sm:hidden"><x-logo compact/></span>
            </a>

            <nav class="ml-4 hidden items-center gap-1 lg:flex" aria-label="Shop by category">
                @foreach ($nav as $item)
                    <a href="{{ $item['url'] }}" @class([
                        'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                        'text-ink' => $item['active'],
                        'text-muted hover:text-ink' => ! $item['active'],
                    ]) @if ($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="ml-auto hidden max-w-sm flex-1 md:block">
                <livewire:header-search/>
            </div>

            <div class="ml-auto flex items-center gap-0.5 md:ml-0">
                <x-theme-toggle class="hidden sm:inline-flex"/>
                @auth
                    <a href="{{ route('account.wishlist') }}" class="btn-icon hidden sm:inline-flex" aria-label="Wishlist"><x-icon name="heart"/></a>
                @endauth

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="btn-icon" @click="open = !open" :aria-expanded="open" aria-label="Account menu">
                        <x-icon name="user"/>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right
                        class="absolute right-0 mt-2 w-60 overflow-hidden rounded-xl border border-line bg-raised py-1.5 text-sm shadow-xl shadow-black/10">
                        @auth
                            <div class="border-b border-line px-4 py-2.5">
                                <p class="font-medium">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                            </div>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-surface"><x-icon name="dashboard" :size="16"/> Store admin</a>
                            @endif
                            <a href="{{ route('account.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-surface"><x-icon name="user" :size="16"/> My account</a>
                            <a href="{{ route('account.orders.index') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-surface"><x-icon name="package" :size="16"/> Orders</a>
                            <a href="{{ route('account.wishlist') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-surface"><x-icon name="heart" :size="16"/> Wishlist</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-line pt-1.5 mt-1.5">
                                @csrf
                                <button class="flex w-full items-center gap-2.5 px-4 py-2 text-left hover:bg-surface"><x-icon name="log-out" :size="16"/> Sign out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-surface">Sign in</a>
                            <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-surface">Create an account</a>
                        @endauth
                    </div>
                </div>

                <a href="{{ route('cart') }}" class="btn-icon relative" aria-label="Cart"
                    x-data="{ count: {{ $cartCount }} }" @cart-updated.window="count = $event.detail.count">
                    <x-icon name="bag"/>
                    <span x-show="count > 0" x-text="count" @if ($cartCount === 0) x-cloak @endif
                        class="absolute -top-0.5 -right-0.5 grid min-w-5 place-items-center rounded-full bg-accent px-1 text-[11px] leading-5 font-semibold text-accent-ink">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Full-screen menu on phones and tablets. --}}
    <div x-show="menu" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex flex-col bg-bg lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
        <div class="wrap flex h-16 items-center justify-between border-b border-line">
            <x-logo/>
            <button type="button" class="btn-icon -mr-2" @click="menu = false" aria-label="Close menu"><x-icon name="x"/></button>
        </div>
        <div class="wrap flex flex-1 flex-col gap-6 overflow-y-auto py-6">
            <livewire:header-search/>
            <nav class="flex flex-col" aria-label="Menu">
                @foreach ($nav as $item)
                    <a href="{{ $item['url'] }}" class="flex items-center justify-between border-b border-line py-4 text-xl font-medium">
                        {{ $item['label'] === 'All filters' ? 'All filters' : $item['label'].' filters' }} <x-icon name="chevron-right" class="text-muted"/>
                    </a>
                @endforeach
                <a href="{{ route('about') }}" class="flex items-center justify-between border-b border-line py-4 text-xl font-medium">About us <x-icon name="chevron-right" class="text-muted"/></a>
                <a href="{{ route('contact') }}" class="flex items-center justify-between border-b border-line py-4 text-xl font-medium">Contact <x-icon name="chevron-right" class="text-muted"/></a>
            </nav>
            <div class="mt-auto flex items-center justify-between">
                <span class="text-sm text-muted">Theme</span>
                <x-theme-toggle/>
            </div>
        </div>
    </div>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-24 border-t border-line bg-surface">
        <div class="wrap grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-3">
                <x-logo/>
                <p class="max-w-xs text-sm text-muted">{{ config('store.tagline') }}. Every listing names the engines and model years it fits.</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold">Shop</h2>
                <ul class="mt-3 space-y-2 text-sm text-muted">
                    @foreach ($nav as $item)
                        <li><a href="{{ $item['url'] }}" class="link font-normal text-muted hover:text-ink">{{ $item['label'] === 'All filters' ? 'All filters' : $item['label'].' filters' }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h2 class="text-sm font-semibold">Your account</h2>
                <ul class="mt-3 space-y-2 text-sm text-muted">
                    <li><a href="{{ route('account.orders.index') }}" class="link font-normal text-muted hover:text-ink">Track an order</a></li>
                    <li><a href="{{ route('account.wishlist') }}" class="link font-normal text-muted hover:text-ink">Wishlist</a></li>
                    <li><a href="{{ route('account.addresses.index') }}" class="link font-normal text-muted hover:text-ink">Addresses</a></li>
                    <li><a href="{{ route('about') }}" class="link font-normal text-muted hover:text-ink">About us</a></li>
                </ul>
            </div>
            <div>
                <h2 class="text-sm font-semibold">Visit or write</h2>
                <ul class="mt-3 space-y-2.5 text-sm text-muted">
                    <li class="flex gap-2"><x-icon name="map-pin" :size="16" class="mt-0.5"/> {{ config('store.contact.area') }}</li>
                    <li class="flex gap-2"><x-icon name="clock" :size="16" class="mt-0.5"/> {{ config('store.contact.hours') }}</li>
                    <li class="flex gap-2"><x-icon name="mail" :size="16" class="mt-0.5"/> <a href="{{ route('contact') }}" class="link font-normal text-muted hover:text-ink">Send us a message</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-line">
            <p class="wrap py-5 text-xs text-muted">© {{ now()->year }} {{ config('store.name') }}. Prices in Bangladeshi taka.</p>
        </div>
    </footer>

    <x-toasts/>
</body>
</html>
