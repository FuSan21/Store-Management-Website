<div class="wrap py-10">
    <h1 class="text-3xl font-semibold sm:text-4xl">Your cart</h1>

    @if ($cart->isEmpty())
        <x-empty-state icon="bag" title="Your cart is empty" body="Find the right filter by choosing your car's make, model and engine." class="mt-8">
            <a href="{{ route('shop') }}" class="btn btn-primary">Browse filters</a>
        </x-empty-state>
    @else
        <div class="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_380px]">
            <ul class="divide-y divide-line border-y border-line" wire:loading.class="opacity-60">
                @foreach ($cart->lines() as $line)
                    @php($product = $line->product)
                    <li class="flex gap-4 py-5 sm:gap-6" wire:key="line-{{ $product->bmuk_no }}">
                        <a href="{{ route('products.show', $product) }}" class="zoom size-24 shrink-0 overflow-hidden rounded-xl bg-surface sm:size-28">
                            <img src="{{ $product->thumb_url }}" alt="{{ $product->title }}" class="size-full object-contain p-3">
                        </a>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <div class="flex justify-between gap-4">
                                <div class="min-w-0">
                                    <a href="{{ route('products.show', $product) }}" class="font-semibold hover:underline">{{ $product->collection }}</a>
                                    <p class="text-sm text-muted">{{ $product->car_brand }} {{ $product->model }} · {{ $product->filter_type }}</p>
                                    <p class="text-xs text-muted">BMUK {{ $product->bmuk_no }} · {{ money($product->price) }} each</p>
                                </div>
                                <p class="font-semibold whitespace-nowrap">{{ money($line->total()) }}</p>
                            </div>

                            @if ($line->exceedsStock())
                                <p class="mt-2 flex items-center gap-1.5 text-sm text-accent"><x-icon name="alert" :size="16"/> Only {{ $product->stock }} left — lower the quantity to check out.</p>
                            @endif

                            <div class="mt-auto flex items-center justify-between pt-3">
                                <div class="inline-flex items-center rounded-lg border border-line" role="group" aria-label="Quantity">
                                    <button type="button" class="btn-icon size-9" wire:click="decrement({{ $product->bmuk_no }})" aria-label="One fewer"><x-icon name="minus" :size="16"/></button>
                                    <span class="w-9 text-center text-sm font-medium tabular-nums">{{ $line->quantity }}</span>
                                    <button type="button" class="btn-icon size-9" wire:click="increment({{ $product->bmuk_no }})" @disabled($line->quantity >= $product->stock) aria-label="One more"><x-icon name="plus" :size="16"/></button>
                                </div>
                                <button type="button" wire:click="remove({{ $product->bmuk_no }})" class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-accent">
                                    <x-icon name="trash" :size="16"/> Remove
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <aside class="card p-6 lg:sticky lg:top-24" aria-label="Order summary">
                <h2 class="text-lg font-semibold">Order summary</h2>

                @if ($cart->qualifiesForFreeShipping())
                    <p class="mt-4 flex items-center gap-2 rounded-lg bg-ok-soft px-3 py-2.5 text-sm text-ok"><x-icon name="truck" :size="16"/> Your order ships free.</p>
                @else
                    @php($progress = min(100, (1 - $cart->amountToFreeShipping() / config('store.free_shipping_threshold')) * 100))
                    <div class="mt-4">
                        <p class="text-sm">Add <strong>{{ money($cart->amountToFreeShipping()) }}</strong> more for free delivery.</p>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-surface" role="progressbar" aria-valuenow="{{ (int) $progress }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="h-full rounded-full bg-accent transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @endif

                <dl class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-medium">{{ money($cart->subtotal()) }}</dd></div>
                    @if ($coupon = $cart->coupon())
                        <div class="flex justify-between">
                            <dt class="flex items-center gap-2 text-muted">
                                Code <span class="rounded bg-surface px-1.5 py-0.5 font-mono text-xs text-ink">{{ $coupon->code }}</span>
                                <button type="button" wire:click="removeCoupon" class="text-xs underline hover:text-accent">Remove</button>
                            </dt>
                            <dd class="font-medium text-ok">−{{ money($cart->discount()) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between"><dt class="text-muted">Delivery</dt><dd class="text-muted">At checkout</dd></div>
                    <div class="flex justify-between border-t border-line pt-3 text-base"><dt class="font-semibold">Estimated total</dt><dd class="font-semibold">{{ money($cart->total()) }}</dd></div>
                </dl>

                @unless ($cart->coupon())
                    <form wire:submit="applyCoupon" class="mt-6">
                        <label for="coupon" class="label">Discount code</label>
                        <div class="flex gap-2">
                            <input id="coupon" wire:model="couponCode" class="input uppercase" placeholder="WELCOME10" autocomplete="off" @error('couponCode') aria-invalid="true" @enderror>
                            <button class="btn btn-secondary" wire:loading.attr="disabled" wire:target="applyCoupon">Apply</button>
                        </div>
                        @error('couponCode') <p class="mt-1.5 text-sm text-accent">{{ $message }}</p> @enderror
                    </form>
                @endunless

                <a href="{{ route('checkout') }}" @class(['btn btn-primary btn-lg mt-6 w-full', 'pointer-events-none opacity-50' => $cart->lines()->contains(fn ($l) => $l->exceedsStock())])>
                    Continue to checkout <x-icon name="arrow-right" :size="18"/>
                </a>
                @guest
                    <p class="mt-3 text-center text-xs text-muted">You'll sign in or create an account at checkout. Your cart comes with you.</p>
                @endguest
                <ul class="mt-6 space-y-2 border-t border-line pt-5 text-sm text-muted">
                    <li class="flex items-center gap-2"><x-icon name="wallet" :size="16"/> bKash, Nagad, card or cash on delivery</li>
                    <li class="flex items-center gap-2"><x-icon name="truck" :size="16"/> Steadfast, Sundarban or eCourier</li>
                </ul>
            </aside>
        </div>
    @endif
</div>
