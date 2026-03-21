@props(['title', 'subtitle' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title, 'description' => null])
</head>
<body class="min-h-dvh bg-bg text-ink">
    <div class="grid min-h-dvh lg:grid-cols-2">
        <div class="flex flex-col px-5 py-8 sm:px-10">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" aria-label="{{ config('store.name') }} home"><x-logo/></a>
                <x-theme-toggle/>
            </div>
            <main class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center py-12">
                <h1 class="text-3xl font-semibold">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-2 text-muted">{{ $subtitle }}</p>
                @endif
                <div class="mt-8">{{ $slot }}</div>
            </main>
            <p class="text-xs text-muted">© {{ now()->year }} {{ config('store.name') }}</p>
        </div>
        <div class="relative hidden overflow-hidden bg-surface lg:block" aria-hidden="true">
            <div class="absolute inset-0 grid grid-cols-2 grid-rows-2 gap-6 p-16">
                <img src="{{ asset('images/products/air-filter-sm.webp') }}" alt="" class="size-full object-contain">
                <img src="{{ asset('images/products/oil-filter-sm.webp') }}" alt="" class="size-full object-contain">
                <img src="{{ asset('images/products/cabin-filter-sm.webp') }}" alt="" class="size-full object-contain">
                <img src="{{ asset('images/products/fuel-filter-sm.webp') }}" alt="" class="size-full object-contain">
            </div>
        </div>
    </div>
    <x-toasts/>
</body>
</html>
