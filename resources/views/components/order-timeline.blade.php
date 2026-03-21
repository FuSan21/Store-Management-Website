@props(['order'])
@php
    $history = $order->statusHistories;
    $reachedAt = $history->groupBy(fn ($h) => $h->status->value)->map(fn ($items) => $items->last()->created_at);
    $offPath = in_array($order->status, [\App\Enums\OrderStatus::Cancelled, \App\Enums\OrderStatus::Suspended], true);
    $steps = \App\Enums\OrderStatus::timeline();
    $currentIndex = array_search($order->status, $steps, true);
@endphp
<ol {{ $attributes->merge(['class' => 'relative grid gap-6 sm:grid-cols-4 sm:gap-2']) }}>
    @foreach ($steps as $i => $step)
        @php
            $done = ! $offPath && $currentIndex !== false && $i <= $currentIndex;
            $current = ! $offPath && $i === $currentIndex;
            $at = $reachedAt[$step->value] ?? null;
        @endphp
        <li class="relative flex gap-3 sm:flex-col sm:gap-3">
            @if (! $loop->last)
                <span aria-hidden="true" @class([
                    'absolute top-8 left-[15px] h-[calc(100%-8px)] w-0.5 sm:top-[15px] sm:left-8 sm:h-0.5 sm:w-[calc(100%-24px)]',
                    'bg-ink' => $done && ! $current,
                    'bg-line' => ! $done || $current,
                ])></span>
            @endif
            <span @class([
                'relative z-10 grid size-8 shrink-0 place-items-center rounded-full border-2',
                'border-ink bg-ink text-bg' => $done,
                'border-line bg-raised text-muted' => ! $done,
            ])>
                @if ($done && ! $current)
                    <x-icon name="check" :size="16"/>
                @else
                    <span class="text-xs font-semibold">{{ $i + 1 }}</span>
                @endif
            </span>
            <span>
                <span @class(['block text-sm font-semibold', 'text-muted' => ! $done])>{{ $step === \App\Enums\OrderStatus::WaitingForPayment ? 'Order placed' : $step->label() }}</span>
                <span class="block text-xs text-muted">{{ $at ? $at->format('j M, g:i a') : ($done ? '' : 'Pending') }}</span>
            </span>
        </li>
    @endforeach
</ol>
@if ($offPath)
    <p class="mt-5 flex items-center gap-2 rounded-lg bg-surface px-4 py-3 text-sm">
        <x-icon name="alert" :size="16"/>
        This order is {{ strtolower($order->status->label()) }}{{ ($at = $reachedAt[$order->status->value] ?? null) ? ' since '.$at->format('j M Y') : '' }}.
    </p>
@endif
