<x-layouts.account title="Write a review">
    <form method="POST" action="{{ route('account.reviews.store', [$order, $product]) }}" class="card max-w-2xl space-y-6 p-6" x-data="{ rating: {{ (int) old('rating', 0) }}, hover: 0 }">
        @csrf
        <div class="flex items-center gap-4">
            <img src="{{ $product->thumb_url }}" alt="" class="size-16 rounded-lg bg-surface object-contain p-2">
            <div>
                <p class="font-semibold">{{ $product->title }}</p>
                <p class="text-sm text-muted">From order {{ $order->number() }}</p>
            </div>
        </div>

        <fieldset>
            <legend class="label">Your rating</legend>
            <div class="flex gap-1" @mouseleave="hover = 0">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer" @mouseenter="hover = {{ $i }}">
                        <input type="radio" name="rating" value="{{ $i }}" x-model.number="rating" class="sr-only">
                        <span class="sr-only">{{ $i }} {{ Str::plural('star', $i) }}</span>
                        <svg width="32" height="32" viewBox="0 0 24 24" aria-hidden="true" class="transition-transform hover:scale-110"
                            :class="(hover || rating) >= {{ $i }} ? 'text-accent' : 'text-line'">
                            <path fill="currentColor" d="M11.52 2.3a.53.53 0 0 1 .95 0l2.31 4.68a2.12 2.12 0 0 0 1.6 1.16l5.16.76a.53.53 0 0 1 .3.9l-3.74 3.64a2.12 2.12 0 0 0-.61 1.88l.88 5.14a.53.53 0 0 1-.77.56l-4.62-2.43a2.12 2.12 0 0 0-1.97 0L6.4 21.01a.53.53 0 0 1-.77-.56l.88-5.13a2.12 2.12 0 0 0-.61-1.88L2.16 9.8a.53.53 0 0 1 .29-.9l5.17-.76a2.12 2.12 0 0 0 1.6-1.16z"/>
                        </svg>
                    </label>
                @endfor
            </div>
            @error('rating') <p class="mt-1.5 text-sm text-accent">{{ $message }}</p> @enderror
        </fieldset>

        <x-field label="How did it fit and perform?" name="comment" type="textarea" rows="5" placeholder="Car, engine, and anything another driver should know." required/>

        <div class="flex gap-3">
            <button class="btn btn-primary">Publish review</button>
            <a href="{{ route('account.orders.show', $order) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.account>
