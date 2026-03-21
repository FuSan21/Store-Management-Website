<x-layouts.store title="Car filters delivered across Bangladesh">
    {{-- Hero: what is sold, for whom, one hard fact, the finder as the main action. --}}
    <section class="border-b border-line">
        <div class="wrap grid items-center gap-10 py-12 lg:min-h-[calc(100dvh-7.5rem)] lg:grid-cols-[1.05fr_1fr] lg:py-16">
            <div>
                <p class="eyebrow">Oil · Air · Fuel · Cabin</p>
                <h1 class="mt-4 text-4xl leading-[1.05] font-semibold sm:text-5xl xl:text-6xl">
                    Filters that fit your car, delivered across Bangladesh
                </h1>
                <p class="mt-5 max-w-xl text-lg text-muted">
                    {{ $stats['products'] }} filters for {{ $stats['brands'] }} makes, each listed with the engines and model years it fits.
                    Pay by bKash, Nagad, card or cash on delivery.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">Shop all filters <x-icon name="arrow-right" :size="18"/></a>
                    <a href="#parts" class="btn btn-secondary btn-lg">Browse by part</a>
                </div>
            </div>

            <div class="grid items-center gap-4 rounded-[2rem] bg-surface p-4 sm:grid-cols-[1fr_320px] sm:p-8">
                <div class="hidden sm:block">
                    <img src="{{ asset('images/products/oil-filter.webp') }}" alt="A spin-on oil filter" width="688" height="1000"
                        class="mx-auto max-h-[440px] w-auto object-contain dark:hidden" fetchpriority="high">
                    <img src="{{ asset('images/products/oil-filter-dark.webp') }}" alt="A spin-on oil filter" width="688" height="1000"
                        class="mx-auto hidden max-h-[440px] w-auto object-contain dark:block">
                </div>
                <livewire:vehicle-finder/>
            </div>
        </div>
    </section>

    {{-- Shop by part: a different product picture per tile. --}}
    <section id="parts" class="wrap scroll-mt-24 py-20">
        <div class="flex items-end justify-between gap-4">
            <h2 class="text-3xl font-semibold sm:text-4xl">Shop by part</h2>
            <a href="{{ route('shop') }}" class="link hidden text-sm sm:inline">All filters</a>
        </div>
        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($collections as $collection)
                @php($render = (new \App\Models\Product(['collection' => $collection->collection]))->renderName())
                <a href="{{ route('shop', ['collection' => $collection->collection]) }}" class="group card lift zoom overflow-hidden">
                    <div class="aspect-[4/3] overflow-hidden bg-surface">
                        <img src="{{ asset("images/products/{$render}-sm.webp") }}" alt="" loading="lazy" class="size-full object-contain p-6 sm:p-10">
                    </div>
                    <div class="flex items-center justify-between gap-2 p-4">
                        <div>
                            <h3 class="font-semibold">{{ Str::plural($collection->collection) }}</h3>
                            <p class="text-sm text-muted">{{ $collection->products }} products · from {{ money($collection->from_price) }}</p>
                        </div>
                        <x-icon name="arrow-right" :size="18" class="text-muted transition-transform group-hover:translate-x-0.5"/>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @if ($bestSellers->isNotEmpty())
        <section class="wrap py-20">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-3xl font-semibold sm:text-4xl">Best sellers this season</h2>
                <a href="{{ route('shop', ['sort' => 'rating']) }}" class="link hidden text-sm sm:inline">Best rated</a>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($bestSellers->take(4) as $product)
                    <x-product-card :product="$product"/>
                @endforeach
            </div>
        </section>
    @endif

    {{-- How buying works: three plain facts. --}}
    <section class="border-y border-line bg-surface">
        <div class="wrap grid gap-10 py-16 md:grid-cols-3">
            <div>
                <span class="grid size-11 place-items-center rounded-xl bg-raised text-accent"><x-icon name="car"/></span>
                <h3 class="mt-4 text-lg font-semibold">Fitment on every listing</h3>
                <p class="mt-2 text-muted">Each filter lists engine size, fuel and model years. Pick your car and the shop shows only what fits.</p>
            </div>
            <div>
                <span class="grid size-11 place-items-center rounded-xl bg-raised text-accent"><x-icon name="wallet"/></span>
                <h3 class="mt-4 text-lg font-semibold">Pay the way you already do</h3>
                <p class="mt-2 text-muted">bKash, Nagad, debit or credit card, or cash to the courier when the parcel arrives.</p>
            </div>
            <div>
                <span class="grid size-11 place-items-center rounded-xl bg-raised text-accent"><x-icon name="truck"/></span>
                <h3 class="mt-4 text-lg font-semibold">Delivery in 1 to 4 days</h3>
                <p class="mt-2 text-muted">Steadfast inside Dhaka, Sundarban and eCourier nationwide. Free over {{ money(config('store.free_shipping_threshold')) }}.</p>
            </div>
        </div>
    </section>

    <section class="wrap py-20">
        <div class="flex items-end justify-between gap-4">
            <h2 class="text-3xl font-semibold sm:text-4xl">New in the shop</h2>
            <a href="{{ route('shop', ['sort' => 'newest']) }}" class="link hidden text-sm sm:inline">See everything new</a>
        </div>
        <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach ($newArrivals->take(4) as $product)
                <x-product-card :product="$product"/>
            @endforeach
        </div>
    </section>

    @if ($recentlyViewed->isNotEmpty())
        <section class="wrap pb-4">
            <h2 class="text-2xl font-semibold">Pick up where you left off</h2>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($recentlyViewed->take(4) as $product)
                    <x-product-card :product="$product"/>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.store>
