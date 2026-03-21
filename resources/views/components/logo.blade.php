@props(['compact' => false])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg width="30" height="30" viewBox="0 0 32 32" aria-hidden="true" class="shrink-0">
        <rect width="32" height="32" rx="7" fill="var(--c-accent)"/>
        <circle cx="16" cy="16" r="9" fill="none" stroke="#fff" stroke-width="2.5"/>
        <circle cx="16" cy="16" r="3.2" fill="#fff"/>
        <path d="M16 7v3M16 22v3M7 16h3M22 16h3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
    </svg>
    @unless ($compact)
        <span class="text-[15px] leading-tight font-semibold tracking-tight">{{ config('store.name') }}</span>
    @endunless
</span>
