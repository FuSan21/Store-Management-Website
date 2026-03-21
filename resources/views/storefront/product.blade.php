@php
    $rating = $product->reviews->avg('rating') ?? 0;
    $reviewCount = $product->reviews->count();
@endphp
<x-layouts.store :title="$product->title" :description="$product->title.' — '.$product->filter_type.', BMUK '.$product->bmuk_no.'. '.money($product->price).'.'">
    <div class="wrap py-8">
        <nav aria-label="Breadcrumb" class="text-sm text-muted">
            <ol class="flex flex-wrap items-center gap-1.5">
                <li><a href="{{ route('shop') }}" class="hover:text-ink">Shop</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('shop', ['collection' => $product->collection]) }}" class="hover:text-ink">{{ Str::plural($product->collection) }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('shop', ['brand' => $product->car_brand, 'model' => $product->model]) }}" class="hover:text-ink">{{ $product->car_brand }} {{ $product->model }}</a></li>
            </ol>
        </nav>

        <div class="mt-6 grid gap-10 lg:grid-cols-2 lg:gap-14">
            {{-- Gallery --}}
            <div x-data="gallery(@js($product->images))" @keydown.arrow-right="show(active + 1)" @keydown.arrow-left="show(active - 1)">
                <div class="relative aspect-square overflow-hidden rounded-2xl bg-surface">
                    <template x-for="(src, i) in images" :key="src">
                        <img :src="src" x-show="active === i" alt="{{ $product->title }}" class="absolute inset-0 size-full object-contain p-10 sm:p-16">
                    </template>
                    <noscript><img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="absolute inset-0 size-full object-contain p-16"></noscript>
                </div>
                @if (count($product->images) > 1)
                    <div class="mt-3 grid grid-cols-5 gap-3" role="tablist" aria-label="Product pictures">
                        @foreach ($product->images as $i => $src)
                            <button type="button" role="tab" @click="show({{ $i }})" :aria-selected="active === {{ $i }}"
                                :class="active === {{ $i }} ? 'border-ink' : 'border-transparent'"
                                class="aspect-square overflow-hidden rounded-xl border-2 bg-surface">
                                <img src="{{ $src }}" alt="" class="size-full object-contain p-2">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Buy box --}}
            <div>
                <p class="eyebrow">{{ $product->car_brand }} {{ $product->model }}</p>
                <h1 class="mt-2 text-3xl font-semibold sm:text-4xl">{{ $product->collection }} — {{ $product->filter_type }}</h1>
                <a href="#reviews" class="mt-3 inline-block"><x-stars :rating="$rating" :count="$reviewCount" :size="16"/></a>

                <p class="mt-6 text-3xl font-semibold">{{ money($product->price) }}</p>

                <div class="mt-3 text-sm">
                    @if (! $product->in_stock)
                        <p class="font-medium text-accent">Out of stock</p>
                    @elseif ($product->is_low_stock)
                        <p class="font-medium text-warn">Only {{ $product->stock }} left — order soon</p>
                    @else
                        <p class="flex items-center gap-1.5 font-medium text-ok"><x-icon name="check" :size="16"/> In stock · {{ $product->stock }} available</p>
                    @endif
                </div>

                @if ($product->in_stock)
                    <form method="POST" action="{{ route('cart.add') }}" class="mt-6 flex flex-wrap gap-3" x-data="stepper(1, {{ min($product->stock, config('store.cart_max_quantity')) }})">
                        @csrf
                        <input type="hidden" name="bmuk_no" value="{{ $product->bmuk_no }}">
                        <input type="hidden" name="quantity" :value="value">
                        <div class="inline-flex items-center rounded-lg border border-line" role="group" aria-label="Quantity">
                            <button type="button" class="btn-icon size-12" @click="dec" aria-label="One fewer"><x-icon name="minus" :size="16"/></button>
                            <span class="w-10 text-center font-medium tabular-nums" x-text="value">1</span>
                            <button type="button" class="btn-icon size-12" @click="inc" aria-label="One more"><x-icon name="plus" :size="16"/></button>
                        </div>
                        <button class="btn btn-primary btn-lg flex-1"><x-icon name="bag" :size="18"/> Add to cart</button>
                        <button name="buy_now" value="1" class="btn btn-secondary btn-lg flex-1 sm:flex-none">Buy now</button>
                    </form>
                @endif

                <div class="mt-3">
                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                            @csrf
                            <button class="btn btn-ghost -ml-4">
                                <x-icon name="heart" :size="18" @class(['fill-current text-accent' => $inWishlist])/>
                                {{ $inWishlist ? 'Saved to your wishlist' : 'Save to wishlist' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost -ml-4"><x-icon name="heart" :size="18"/> Sign in to save</a>
                    @endauth
                </div>

                <ul class="mt-6 grid gap-3 border-y border-line py-5 text-sm sm:grid-cols-2">
                    <li class="flex gap-2.5"><x-icon name="truck" :size="18" class="text-muted"/> Free delivery over {{ money(config('store.free_shipping_threshold')) }}</li>
                    <li class="flex gap-2.5"><x-icon name="wallet" :size="18" class="text-muted"/> bKash, Nagad, card or cash on delivery</li>
                </ul>

                <h2 class="mt-8 text-lg font-semibold">Fits these engines</h2>
                @if ($product->fitments)
                    <div class="mt-3 overflow-hidden rounded-xl border border-line">
                        <table class="table-base">
                            <thead class="bg-surface"><tr><th>Engine</th><th>Fuel</th><th>Years</th></tr></thead>
                            <tbody>
                                @foreach ($product->specification as $spec)
                                    <tr>
                                        <td>{{ $product->car_brand }} {{ $product->model }} {{ $spec['engine'] ?? '' }}</td>
                                        <td>{{ $spec['fuelType'] ?? '' }}</td>
                                        <td class="tabular-nums">{{ $spec['year'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-2 text-sm text-muted">Fitment details are being added. Message us with your car and we will confirm.</p>
                @endif

                <h2 class="mt-8 text-lg font-semibold">Part numbers</h2>
                <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                    <div><dt class="text-muted">BMUK</dt><dd class="font-medium tabular-nums">{{ $product->bmuk_no }}</dd></div>
                    <div><dt class="text-muted">Euro ID</dt><dd class="font-medium">{{ $product->euro_id }}</dd></div>
                    <div><dt class="text-muted">OEM</dt><dd class="font-medium">{{ $product->oem_id }}</dd></div>
                </dl>

                @if ($product->description)
                    <div class="mt-8 max-w-prose text-muted">{!! nl2br(e($product->description)) !!}</div>
                @endif
            </div>
        </div>

        {{-- Reviews --}}
        <section id="reviews" class="mt-20 scroll-mt-24 border-t border-line pt-14">
            <div class="grid gap-10 lg:grid-cols-[300px_1fr]">
                <div>
                    <h2 class="text-2xl font-semibold">Reviews from buyers</h2>
                    @if ($reviewCount)
                        <p class="mt-4 text-5xl font-semibold">{{ number_format($rating, 1) }}</p>
                        <x-stars :rating="$rating" :size="18" class="mt-2"/>
                        <p class="mt-1 text-sm text-muted">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }} from verified orders</p>
                        <ul class="mt-6 space-y-2">
                            @foreach ([5, 4, 3, 2, 1] as $stars)
                                @php($n = $ratingCounts[$stars] ?? 0)
                                <li class="flex items-center gap-3 text-sm">
                                    <span class="w-12 text-muted">{{ $stars }} star</span>
                                    <span class="h-2 flex-1 overflow-hidden rounded-full bg-surface"><span class="block h-full rounded-full bg-ink" style="width: {{ $reviewCount ? $n / $reviewCount * 100 : 0 }}%"></span></span>
                                    <span class="w-6 text-right tabular-nums text-muted">{{ $n }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <p class="mt-6 text-sm text-muted">Only customers whose order was delivered can review, from their order page.</p>
                </div>
                <div>
                    @forelse ($product->reviews->take(10) as $review)
                        <article class="border-b border-line py-6 first:pt-0">
                            <div class="flex items-center justify-between gap-4">
                                <x-stars :rating="$review->rating"/>
                                <time class="text-xs text-muted" datetime="{{ $review->created_at?->toIso8601String() }}">{{ $review->created_at?->format('j M Y') }}</time>
                            </div>
                            <p class="mt-3">{{ $review->comment }}</p>
                            <p class="mt-2 text-sm text-muted">{{ $review->user?->name ?? 'A customer' }} · Verified purchase</p>
                        </article>
                    @empty
                        <x-empty-state icon="star" title="No reviews yet" body="Bought this filter? Review it from your order once it is delivered."/>
                    @endforelse
                </div>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section class="mt-20">
                <h2 class="text-2xl font-semibold">Also for {{ $product->car_brand }} {{ $product->model }}</h2>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item"/>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($recentlyViewed->isNotEmpty())
            <section class="mt-20">
                <h2 class="text-2xl font-semibold">Recently viewed</h2>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($recentlyViewed as $item)
                        <x-product-card :product="$item"/>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.store>
