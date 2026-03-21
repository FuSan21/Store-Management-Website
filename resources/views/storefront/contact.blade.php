<x-layouts.store title="Contact">
    <section class="wrap grid gap-12 py-16 lg:grid-cols-[1fr_1.2fr] lg:py-24">
        <div>
            <p class="eyebrow">Contact</p>
            <h1 class="mt-3 text-4xl leading-tight font-semibold sm:text-5xl">Ask us which filter fits</h1>
            <p class="mt-6 text-lg text-muted">Send your car's make, model, engine and year, or the number printed on your old filter. We reply by email within one working day.</p>
            <ul class="mt-10 space-y-5">
                <li class="flex gap-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-surface text-accent"><x-icon name="map-pin"/></span>
                    <span><span class="block font-medium">Counter</span><span class="text-muted">{{ config('store.contact.area') }}</span></span>
                </li>
                <li class="flex gap-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-surface text-accent"><x-icon name="clock"/></span>
                    <span><span class="block font-medium">Hours</span><span class="text-muted">{{ config('store.contact.hours') }}</span></span>
                </li>
                <li class="flex gap-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-surface text-accent"><x-icon name="package"/></span>
                    <span><span class="block font-medium">An order question?</span><span class="text-muted">Open the order from <a href="{{ route('account.orders.index') }}" class="link">your orders</a> — its number helps us answer faster.</span></span>
                </li>
            </ul>
        </div>

        <form method="POST" action="{{ route('contact.send') }}" class="card space-y-5 p-6 sm:p-8">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field label="Your name" name="name" :value="auth()->user()?->name" autocomplete="name" required/>
                <x-field label="Email" name="email" type="email" :value="auth()->user()?->email" autocomplete="email" required/>
            </div>
            <x-field label="Your car (optional)" name="vehicle" placeholder="Toyota Axio 1.5L petrol, 2016"/>
            <x-field label="Message" name="message" type="textarea" rows="6" required/>
            <div class="hidden" aria-hidden="true">
                <label for="website">Leave this empty</label>
                <input id="website" name="website" tabindex="-1" autocomplete="off">
            </div>
            <button class="btn btn-primary btn-lg w-full sm:w-auto">Send message</button>
        </form>
    </section>
</x-layouts.store>
