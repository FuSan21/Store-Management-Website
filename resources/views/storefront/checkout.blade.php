@php
    $default = $addresses->firstWhere('is_default_shipping', true) ?? $addresses->first();
    $selectedAddress = old('shipping_address_id', $default?->id ?? 'new');
    $costs = collect($shippingMethods)->mapWithKeys(fn ($m) => [$m->value => $cart->shippingCost($m)]);
    $base = $cart->subtotal() - $cart->discount();
@endphp
<x-layouts.store title="Checkout">
    <div class="wrap py-10">
        <a href="{{ route('cart') }}" class="inline-flex items-center gap-1 text-sm text-muted hover:text-ink"><x-icon name="chevron-left" :size="16"/> Back to cart</a>
        <h1 class="mt-3 text-3xl font-semibold sm:text-4xl">Checkout</h1>

        @error('cart')
            <div class="mt-6 flex gap-3 rounded-xl border border-accent bg-accent-soft p-4 text-sm text-accent" role="alert">
                <x-icon name="alert" :size="18"/> {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_400px]"
            x-data="{ address: @js((string) $selectedAddress), shipping: @js(old('shipping_method', $shippingMethods[0]->value)), costs: @js($costs), base: {{ $base }}, currency: @js(config('store.currency')) }">
            @csrf

            <div class="space-y-10">
                {{-- 1. Delivery address --}}
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-semibold"><span class="grid size-7 place-items-center rounded-full bg-ink text-sm text-bg">1</span> Delivery address</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach ($addresses as $address)
                            <label class="card relative flex cursor-pointer gap-3 p-4 transition-colors" :class="address === '{{ $address->id }}' && 'border-ink'">
                                <input type="radio" name="shipping_address_id" value="{{ $address->id }}" x-model="address" class="mt-1 accent-accent">
                                <span class="text-sm">
                                    <span class="block font-medium">{{ $address->label }}</span>
                                    <span class="mt-1 block text-muted">{{ $address->recipient }}<br>{{ $address->line }}, {{ $address->city }}</span>
                                    @if ($address->phone)<span class="mt-1 block text-muted">{{ $address->phone }}</span>@endif
                                </span>
                            </label>
                        @endforeach
                        <label class="card flex cursor-pointer items-center gap-3 p-4 transition-colors" :class="address === 'new' && 'border-ink'">
                            <input type="radio" value="new" x-model="address" class="accent-accent" @if ($addresses->isEmpty()) checked @endif>
                            <span class="text-sm font-medium">{{ $addresses->isEmpty() ? 'Enter your address' : 'Use a new address' }}</span>
                        </label>
                    </div>
                    @error('shipping_address_id') <p class="mt-2 text-sm text-accent">{{ $message }}</p> @enderror

                    <div x-show="address === 'new'" x-cloak class="mt-5 grid gap-4 sm:grid-cols-2">
                        <x-field label="Recipient" name="new_address[recipient]" error-key="new_address.recipient" :value="auth()->user()->name" autocomplete="name" x-bind:disabled="address !== 'new'" class="sm:col-span-2"/>
                        <x-field label="Street and area" name="new_address[line]" error-key="new_address.line" autocomplete="street-address" placeholder="Road 27, Dhanmondi" x-bind:disabled="address !== 'new'" class="sm:col-span-2"/>
                        <x-field label="City" name="new_address[city]" error-key="new_address.city" autocomplete="address-level2" placeholder="Dhaka" x-bind:disabled="address !== 'new'"/>
                        <label class="flex items-center gap-2.5 self-end pb-3 text-sm">
                            <input type="checkbox" name="save_address" value="1" class="checkbox" checked x-bind:disabled="address !== 'new'"> Save to my address book
                        </label>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <x-field label="Mobile number for the courier" name="phone" type="tel" :value="$default?->phone" autocomplete="tel" placeholder="01712345678" inputmode="tel" required/>
                    </div>
                </section>

                {{-- 2. Delivery method --}}
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-semibold"><span class="grid size-7 place-items-center rounded-full bg-ink text-sm text-bg">2</span> Courier</h2>
                    <div class="mt-5 grid gap-3">
                        @foreach ($shippingMethods as $method)
                            <label class="card flex cursor-pointer items-center gap-4 p-4 transition-colors" :class="shipping === '{{ $method->value }}' && 'border-ink'">
                                <input type="radio" name="shipping_method" value="{{ $method->value }}" x-model="shipping" class="accent-accent">
                                <span class="flex-1 text-sm">
                                    <span class="block font-medium">{{ $method->label() }}</span>
                                    <span class="text-muted">{{ $method->eta() }}</span>
                                </span>
                                <span class="text-sm font-semibold">{{ $costs[$method->value] > 0 ? money($costs[$method->value]) : 'Free' }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('shipping_method') <p class="mt-2 text-sm text-accent">{{ $message }}</p> @enderror
                </section>

                {{-- 3. Payment --}}
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-semibold"><span class="grid size-7 place-items-center rounded-full bg-ink text-sm text-bg">3</span> Payment</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2" x-data="{ pay: @js(old('payment_method', 'bkash')) }">
                        @foreach ($paymentMethods as $method)
                            <label class="card flex cursor-pointer items-center gap-3 p-4 transition-colors" :class="pay === '{{ $method->value }}' && 'border-ink'">
                                <input type="radio" name="payment_method" value="{{ $method->value }}" x-model="pay" class="accent-accent">
                                <span class="text-sm font-medium">{{ $method->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-sm text-muted">For bKash, Nagad and card we send payment instructions with your confirmation. Cash on delivery is paid to the courier.</p>
                    @error('payment_method') <p class="mt-2 text-sm text-accent">{{ $message }}</p> @enderror

                    <x-field label="Notes for the courier (optional)" name="notes" type="textarea" rows="2" placeholder="Gate code, landmark, best time to call" class="mt-6"/>
                </section>
            </div>

            <aside class="card p-6 lg:sticky lg:top-24" aria-label="Order summary">
                <h2 class="text-lg font-semibold">Your order</h2>
                <ul class="mt-4 divide-y divide-line">
                    @foreach ($cart->lines() as $line)
                        <li class="flex items-center gap-3 py-3">
                            <span class="relative">
                                <img src="{{ $line->product->thumb_url }}" alt="" class="size-14 rounded-lg bg-surface object-contain p-1.5">
                                <span class="absolute -top-1.5 -right-1.5 grid size-5 place-items-center rounded-full bg-ink text-[11px] font-semibold text-bg">{{ $line->quantity }}</span>
                            </span>
                            <span class="min-w-0 flex-1 text-sm">
                                <span class="block truncate font-medium">{{ $line->product->collection }}</span>
                                <span class="block truncate text-muted">{{ $line->product->car_brand }} {{ $line->product->model }}</span>
                            </span>
                            <span class="text-sm font-medium">{{ money($line->total()) }}</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 space-y-2.5 border-t border-line pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ money($cart->subtotal()) }}</dd></div>
                    @if ($cart->discount() > 0)
                        <div class="flex justify-between"><dt class="text-muted">Discount ({{ $cart->coupon()->code }})</dt><dd class="text-ok">−{{ money($cart->discount()) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-muted">Delivery</dt><dd x-text="costs[shipping] > 0 ? currency + costs[shipping].toLocaleString('en-US') : 'Free'"></dd></div>
                    <div class="flex justify-between border-t border-line pt-3 text-base font-semibold">
                        <dt>Total</dt>
                        <dd x-text="currency + (base + costs[shipping]).toLocaleString('en-US', { maximumFractionDigits: 2 })">{{ money($cart->total($shippingMethods[0])) }}</dd>
                    </div>
                </dl>
                <button class="btn btn-primary btn-lg mt-6 w-full">Place order</button>
                <p class="mt-3 text-center text-xs text-muted">You can cancel while the order is waiting for payment.</p>
            </aside>
        </form>
    </div>
</x-layouts.store>
