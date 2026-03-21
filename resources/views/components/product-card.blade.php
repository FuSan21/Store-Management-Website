@props(['product'])
@php
    $reviews = $product->relationLoaded('reviews') ? $product->reviews : collect();
    $rating = $product->reviews_avg_rating ?? $reviews->avg('rating') ?? 0;
@endphp
<article {{ $attributes->merge(['class' => 'group card lift zoom relative flex min-w-0 flex-col overflow-hidden']) }}>
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-square overflow-hidden bg-surface" tabindex="-1" aria-hidden="true">
        <img src="{{ $product->thumb_url }}" alt="" loading="lazy" width="400" height="400" class="size-full object-contain p-5 sm:p-8">
        @if (! $product->in_stock)
            <span class="absolute top-3 left-3 rounded-full bg-ink px-2.5 py-1 text-xs font-medium text-bg">Out of stock</span>
        @elseif ($product->is_low_stock)
            <span class="absolute top-3 left-3 rounded-full bg-warn-soft px-2.5 py-1 text-xs font-medium text-warn">Only {{ $product->stock }} left</span>
        @elseif ($product->is_featured)
            <span class="absolute top-3 left-3 rounded-full bg-accent px-2.5 py-1 text-xs font-medium text-accent-ink">Featured</span>
        @endif
    </a>

    @auth
        <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="absolute top-2 right-2">
            @csrf
            <button class="btn-icon bg-raised/80 backdrop-blur" aria-label="Save {{ $product->title }} to wishlist">
                <x-icon name="heart" :size="18"/>
            </button>
        </form>
    @endauth

    <div class="flex flex-1 flex-col gap-1.5 p-3 sm:gap-2 sm:p-4">
        <p class="eyebrow">{{ $product->car_brand }} · {{ $product->model }}</p>
        <h3 class="text-sm leading-snug font-semibold sm:text-[15px]">
            <a href="{{ route('products.show', $product) }}" class="after:absolute after:inset-0 after:content-['']">{{ $product->collection }}</a>
        </h3>
        <p class="text-xs text-muted sm:text-sm">{{ $product->filter_type }} · BMUK {{ $product->bmuk_no }}</p>
        <x-stars :rating="$rating" :count="$reviews->count()" class="mt-auto pt-1"/>
        <div class="relative z-10 flex items-center justify-between gap-2 pt-2">
            <span class="text-base font-semibold sm:text-lg">{{ money($product->price) }}</span>
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="bmuk_no" value="{{ $product->bmuk_no }}">
                <button class="btn btn-secondary btn-sm" @disabled(! $product->in_stock) aria-label="Add {{ $product->title }} to cart">
                    <x-icon name="plus" :size="14"/> <span class="max-[379px]:sr-only">Add</span>
                </button>
            </form>
        </div>
    </div>
</article>
