<div class="wrap py-10" x-data="{ filters: false }">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Shop</p>
            <h1 class="mt-1 text-3xl font-semibold sm:text-4xl">
                @if ($brand && $model)
                    Filters for {{ $brand }} {{ $model }}
                @elseif ($brand)
                    {{ $collection ? Str::plural($collection) : 'Filters' }} for {{ $brand }}
                @elseif ($collection)
                    {{ Str::plural($collection) }}
                @else
                    All filters
                @endif
            </h1>
            <p class="mt-2 text-sm text-muted" wire:loading.class="opacity-50">
                {{ $this->products->total() }} {{ Str::plural('product', $this->products->total()) }}
            </p>
        </div>
        <div class="flex w-full items-center gap-2 sm:w-auto">
            <button type="button" class="btn btn-secondary flex-1 lg:hidden" @click="filters = true">
                <x-icon name="sliders" :size="16"/> Filters @if (count($this->chips)) ({{ count($this->chips) }}) @endif
            </button>
            <label for="sort" class="sr-only">Sort by</label>
            <select id="sort" wire:model.live="sort" class="input flex-1 sm:w-56">
                @foreach (\App\Livewire\ShopCatalog::SORTS as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (count($this->chips))
        <div class="mt-6 flex flex-wrap items-center gap-2">
            @foreach ($this->chips as $key => $label)
                <button type="button" wire:click="clear('{{ $key }}')" wire:key="chip-{{ $key }}"
                    class="inline-flex items-center gap-1.5 rounded-full border border-line bg-raised py-1.5 pr-2.5 pl-3.5 text-sm hover:border-ink">
                    {{ $label }} <x-icon name="x" :size="14" class="text-muted"/>
                    <span class="sr-only">Remove filter</span>
                </button>
            @endforeach
            <button type="button" wire:click="clearAll" class="link ml-1 text-sm">Clear all</button>
        </div>
    @endif

    <div class="mt-8 grid gap-8 lg:grid-cols-[260px_1fr]">
        {{-- Filters: a column on desktop, a full-screen sheet on phones. --}}
        <aside :class="filters ? 'fixed inset-0 z-50 overflow-y-auto bg-bg p-5' : 'hidden'" class="lg:static lg:block lg:p-0" aria-label="Filters">
            <div class="mb-6 flex items-center justify-between lg:hidden">
                <h2 class="text-xl font-semibold">Filters</h2>
                <button type="button" class="btn-icon" @click="filters = false" aria-label="Close filters"><x-icon name="x"/></button>
            </div>

            <div class="space-y-7">
                <fieldset>
                    <legend class="text-sm font-semibold">Part</legend>
                    <div class="mt-3 space-y-1">
                        <label class="flex cursor-pointer items-center justify-between rounded-lg px-2 py-1.5 text-sm hover:bg-surface">
                            <span class="flex items-center gap-2.5"><input type="radio" wire:model.live="collection" value="" class="accent-accent"> Every filter</span>
                        </label>
                        @foreach ($this->collections as $name => $count)
                            <label class="flex cursor-pointer items-center justify-between rounded-lg px-2 py-1.5 text-sm hover:bg-surface" wire:key="col-{{ $name }}">
                                <span class="flex items-center gap-2.5"><input type="radio" wire:model.live="collection" value="{{ $name }}" class="accent-accent"> {{ $name }}</span>
                                <span class="text-xs text-muted">{{ $count }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="space-y-3">
                    <legend class="text-sm font-semibold">Your vehicle</legend>
                    <div>
                        <label for="f-brand" class="sr-only">Make</label>
                        <select id="f-brand" wire:model.live="brand" class="input">
                            <option value="">Any make</option>
                            @foreach ($this->brands as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="f-model" class="sr-only">Model</label>
                        <select id="f-model" wire:model.live="model" class="input" @disabled(! $brand)>
                            <option value="">{{ $brand ? 'Any model' : 'Choose a make first' }}</option>
                            @foreach ($this->models as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="f-fit" class="sr-only">Engine</label>
                        <select id="f-fit" wire:model.live="fit" class="input" @disabled(! $model)>
                            <option value="">{{ $model ? 'Any engine' : 'Choose a model first' }}</option>
                            @foreach ($this->fitments as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-sm font-semibold">Filter type</legend>
                    <select wire:model.live="type" class="input mt-3" aria-label="Filter type">
                        <option value="">Any type</option>
                        @foreach ($this->types as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </fieldset>

                <fieldset>
                    <legend class="text-sm font-semibold">Price ({{ config('store.currency') }})</legend>
                    <div class="mt-3 flex items-center gap-2">
                        <input type="number" min="0" inputmode="numeric" wire:model.live.debounce.500ms="min" placeholder="Min" class="input" aria-label="Minimum price">
                        <span class="text-muted">–</span>
                        <input type="number" min="0" inputmode="numeric" wire:model.live.debounce.500ms="max" placeholder="Max" class="input" aria-label="Maximum price">
                    </div>
                </fieldset>

                <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium">
                    <input type="checkbox" wire:model.live="inStock" class="checkbox"> In stock only
                </label>
            </div>

            <button type="button" class="btn btn-primary mt-8 w-full lg:hidden" @click="filters = false">
                Show {{ $this->products->total() }} results
            </button>
        </aside>

        <section aria-label="Results" class="min-w-0">
            <div wire:loading.class="opacity-60 transition-opacity" class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-3">
                @forelse ($this->products as $product)
                    <x-product-card :product="$product" wire:key="p-{{ $product->bmuk_no }}"/>
                @empty
                    <x-empty-state icon="search" title="No filters match" body="Try a different model, remove a filter or search by the part number on your old filter." class="col-span-full">
                        <button type="button" wire:click="clearAll" class="btn btn-secondary">Clear all filters</button>
                    </x-empty-state>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $this->products->links('pagination.livewire') }}
            </div>
        </section>
    </div>
</div>
