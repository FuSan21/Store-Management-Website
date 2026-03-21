<form wire:submit="submit" role="search" class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
    <label for="search-{{ $this->getId() }}" class="sr-only">Search filters</label>
    <x-icon name="search" :size="18" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-muted"/>
    <input id="search-{{ $this->getId() }}" type="search" wire:model.live.debounce.250ms="q" @focus="open = true" @input="open = true"
        autocomplete="off" placeholder="Search by car, part or BMUK number"
        class="input rounded-full bg-surface pl-10 focus:bg-raised">

    @if (mb_strlen(trim($q)) >= 2)
        <div x-show="open" x-cloak x-transition.opacity
            class="absolute inset-x-0 top-full z-50 mt-2 overflow-hidden rounded-xl border border-line bg-raised shadow-xl shadow-black/10">
            @forelse ($this->results as $product)
                <a href="{{ route('products.show', $product) }}" wire:key="r-{{ $product->bmuk_no }}" class="flex items-center gap-3 px-3 py-2.5 hover:bg-surface">
                    <img src="{{ $product->thumb_url }}" alt="" class="size-11 shrink-0 rounded-lg bg-surface object-contain p-1.5">
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium">{{ $product->title }}</span>
                        <span class="block text-xs text-muted">{{ $product->filter_type }} · BMUK {{ $product->bmuk_no }}</span>
                    </span>
                    <span class="text-sm font-semibold">{{ money($product->price) }}</span>
                </a>
            @empty
                <p class="px-4 py-5 text-sm text-muted">No filters match “{{ $q }}”. Try a car model or part number.</p>
            @endforelse
            @if ($this->results->isNotEmpty())
                <button type="submit" class="flex w-full items-center justify-between border-t border-line px-4 py-3 text-sm font-medium hover:bg-surface">
                    See all results for “{{ $q }}” <x-icon name="arrow-right" :size="16"/>
                </button>
            @endif
        </div>
    @endif
</form>
