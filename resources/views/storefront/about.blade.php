<x-layouts.store title="About us">
    <section class="wrap grid items-center gap-12 py-16 lg:grid-cols-2 lg:py-24">
        <div>
            <p class="eyebrow">About us</p>
            <h1 class="mt-3 text-4xl leading-tight font-semibold sm:text-5xl">A filter shop for drivers and workshops in Bangladesh</h1>
            <p class="mt-6 text-lg text-muted">
                We stock oil, air, fuel and cabin filters for the makes most common on our roads:
                {{ $brands->join(', ', ' and ') }}. Every listing carries its BMUK, Euro and OEM numbers
                and the engines it fits, so you can match it to the part in your car before you order.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">Shop filters</a>
                <a href="{{ route('contact') }}" class="btn btn-secondary btn-lg">Ask about a part</a>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="aspect-square rounded-2xl bg-surface p-8"><img src="{{ asset('images/products/air-filter-sm.webp') }}" alt="A panel air filter" class="size-full object-contain"></div>
            <div class="aspect-square rounded-2xl bg-surface p-8"><img src="{{ asset('images/products/fuel-filter-sm.webp') }}" alt="An inline fuel filter" class="size-full object-contain"></div>
            <div class="aspect-square rounded-2xl bg-surface p-8"><img src="{{ asset('images/products/cabin-filter-sm.webp') }}" alt="A cabin air filter" class="size-full object-contain"></div>
            <div class="aspect-square rounded-2xl bg-surface p-8"><img src="{{ asset('images/products/oil-filter-sm.webp') }}" alt="A spin-on oil filter" class="size-full object-contain"></div>
        </div>
    </section>

    <section class="border-y border-line bg-surface">
        <div class="wrap grid gap-10 py-16 md:grid-cols-3">
            <div>
                <h2 class="text-lg font-semibold">How we list a part</h2>
                <p class="mt-2 text-muted">Part numbers first, then the make, model, engine, fuel and years it fits. If a fitment is missing, ask us and we check it.</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold">How we ship</h2>
                <p class="mt-2 text-muted">Steadfast inside Dhaka, Sundarban and eCourier to the rest of the country. We email you at each step, from payment to delivery.</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold">Where to find us</h2>
                <p class="mt-2 text-muted">{{ config('store.contact.area') }}. {{ config('store.contact.hours') }}.</p>
            </div>
        </div>
    </section>
</x-layouts.store>
