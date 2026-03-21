@php
    $editing = $product->exists;
    $fitments = old('specification', $product->specification ?? []);
@endphp
<x-layouts.admin :title="$editing ? 'Edit product '.$product->bmuk_no : 'Add product'">
    <x-slot:actions>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost"><x-icon name="chevron-left" :size="16"/> Products</a>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <form method="POST" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" class="space-y-6" id="product-form">
            @csrf
            @if ($editing) @method('PUT') @endif

            <section class="card space-y-5 p-6">
                <h2 class="font-semibold">Part</h2>
                <div class="grid gap-5 sm:grid-cols-3">
                    <x-field label="BMUK number" name="bmuk_no" type="number" :value="$product->bmuk_no" min="1" required/>
                    <x-field label="Euro ID" name="euro_id" :value="$product->euro_id" required/>
                    <x-field label="OEM ID" name="oem_id" :value="$product->oem_id" required/>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field label="Collection" name="collection" :value="$product->collection" list="collections" placeholder="Oil Filter" required hint="Oil, air, fuel or cabin in the name picks the stand-in picture."/>
                    <x-field label="Filter type" name="filter_type" :value="$product->filter_type" list="filter-types" placeholder="Spin-on" required/>
                </div>
                <x-field label="Description (optional)" name="description" type="textarea" :value="$product->description" rows="4"/>
            </section>

            <section class="card space-y-5 p-6">
                <h2 class="font-semibold">Vehicle and fitment</h2>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field label="Make" name="car_brand" :value="$product->car_brand" list="brands" placeholder="Toyota" required/>
                    <x-field label="Model" name="model" :value="$product->model" list="models" placeholder="Axio" required/>
                </div>
                <div x-data="rows(@js(array_values($fitments)), { engine: '', fuelType: '', year: '' })">
                    <p class="label">Engines this filter fits</p>
                    <div class="space-y-2">
                        <template x-for="(row, i) in rows" :key="i">
                            <div class="grid grid-cols-[1fr_1fr_1fr_auto] gap-2">
                                <input class="input" :name="`specification[${i}][engine]`" x-model="row.engine" placeholder="1.5L" aria-label="Engine">
                                <input class="input" :name="`specification[${i}][fuelType]`" x-model="row.fuelType" placeholder="Petrol" aria-label="Fuel" list="fuels">
                                <input class="input" :name="`specification[${i}][year]`" x-model="row.year" placeholder="2014-2022" aria-label="Years">
                                <button type="button" class="btn-icon text-muted hover:text-accent" @click="remove(i)" aria-label="Remove row"><x-icon name="trash" :size="16"/></button>
                            </div>
                        </template>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm mt-2 -ml-3" @click="add"><x-icon name="plus" :size="14"/> Add an engine</button>
                    @if ($errors->has('specification') || $errors->has('specification.*'))
                        <p class="mt-1.5 text-sm text-accent">{{ $errors->first('specification') ?: collect($errors->get('specification.*'))->flatten()->first() }}</p>
                    @endif
                </div>
            </section>

            <section class="card space-y-5 p-6">
                <h2 class="font-semibold">Pictures</h2>
                <x-field label="Main picture URL" name="image" type="url" :value="$product->image" placeholder="https://…" hint="Leave empty to use the rendered picture for its collection."/>
                <x-field label="More pictures (one URL per line)" name="gallery_urls" error-key="gallery" type="textarea" rows="3" :value="implode(PHP_EOL, $product->gallery ?? [])"/>
                @if ($errors->has('gallery.*'))<p class="text-sm text-accent">{{ collect($errors->get('gallery.*'))->flatten()->first() }}</p>@endif
            </section>

            <datalist id="collections">@foreach ($options['collections'] as $o)<option value="{{ $o }}">@endforeach</datalist>
            <datalist id="filter-types">@foreach ($options['filterTypes'] as $o)<option value="{{ $o }}">@endforeach</datalist>
            <datalist id="brands">@foreach ($options['brands'] as $o)<option value="{{ $o }}">@endforeach</datalist>
            <datalist id="models">@foreach ($options['models'] as $o)<option value="{{ $o }}">@endforeach</datalist>
            <datalist id="fuels"><option value="Petrol"><option value="Diesel"><option value="Hybrid"><option value="CNG"></datalist>
        </form>

        <aside class="space-y-6">
            <section class="card space-y-5 p-6">
                <h2 class="font-semibold">Price and stock</h2>
                <x-field label="Price ({{ config('store.currency') }})" name="price" type="number" step="0.01" min="0" :value="$product->price" form="product-form" required/>
                @unless ($editing)
                    <x-field label="Opening stock" name="stock" type="number" min="0" :value="old('stock', 0)" form="product-form" required/>
                @endunless
                <x-field label="Warn when stock is at or below" name="low_stock_threshold" type="number" min="0" :value="$product->low_stock_threshold" form="product-form" required/>
                <label class="flex items-center gap-2.5 text-sm">
                    <input type="checkbox" name="is_featured" value="1" class="checkbox" form="product-form" @checked(old('is_featured', $product->is_featured))> Feature on the home page and first in the shop
                </label>
                <button form="product-form" class="btn btn-primary w-full">{{ $editing ? 'Save changes' : 'Add product' }}</button>
            </section>

            @if ($editing)
                <section class="card p-6">
                    <div class="flex items-baseline justify-between">
                        <h2 class="font-semibold">Stock</h2>
                        <span class="text-2xl font-semibold tabular-nums">{{ $product->stock }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.products.stock', $product) }}" class="mt-4 space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <x-field label="Change" name="change" type="number" placeholder="+20 or -2" required/>
                            <x-field label="Reason" name="reason" type="select">
                                <option value="restock">Delivery in</option>
                                <option value="adjustment">Correction</option>
                            </x-field>
                        </div>
                        <x-field label="Note" name="note" placeholder="Invoice 4471 from supplier"/>
                        <button class="btn btn-secondary w-full">Record stock change</button>
                    </form>

                    @if ($movements->isNotEmpty())
                        <h3 class="mt-6 text-sm font-semibold">Recent movements</h3>
                        <ul class="mt-2 divide-y divide-line text-sm">
                            @foreach ($movements as $movement)
                                <li class="flex items-center justify-between gap-3 py-2">
                                    <span>
                                        <span class="capitalize">{{ $movement->reason }}</span>
                                        <span class="block text-xs text-muted">{{ $movement->created_at->format('j M, H:i') }} · {{ $movement->user?->name ?? 'System' }}</span>
                                    </span>
                                    <span @class(['font-semibold tabular-nums', 'text-ok' => $movement->change > 0, 'text-accent' => $movement->change < 0])>{{ $movement->change > 0 ? '+' : '' }}{{ $movement->change }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endif
        </aside>
    </div>
</x-layouts.admin>
