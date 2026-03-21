@props(['icon' => 'package', 'title', 'body' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center rounded-xl border border-dashed border-line px-6 py-14 text-center']) }}>
    <span class="mb-4 grid size-12 place-items-center rounded-full bg-surface text-muted">
        <x-icon :name="$icon" :size="22"/>
    </span>
    <h3 class="text-base font-semibold">{{ $title }}</h3>
    @if ($body)
        <p class="mt-1 max-w-sm text-sm text-muted">{{ $body }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
