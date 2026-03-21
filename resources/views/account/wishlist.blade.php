<x-layouts.account title="Wishlist">
    @if ($products->isEmpty())
        <x-empty-state icon="heart" title="Nothing saved yet" body="Tap the heart on any filter to keep it here for your next service.">
            <a href="{{ route('shop') }}" class="btn btn-primary">Browse filters</a>
        </x-empty-state>
    @else
        <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-3">
            @foreach ($products as $product)
                <x-product-card :product="$product"/>
            @endforeach
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</x-layouts.account>
