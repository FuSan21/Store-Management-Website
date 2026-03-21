@php
    $initial = array_values(array_filter([session('toast'), session('status') && session('status') !== 'verification-link-sent' ? ['type' => 'success', 'message' => __(session('status'))] : null]));
@endphp
<div x-data="toasts(@js($initial))" class="pointer-events-none fixed inset-x-0 bottom-0 z-[60] flex flex-col items-center gap-2 p-4 sm:items-end sm:p-6" aria-live="polite">
    <template x-for="toast in items" :key="toast.id">
        <div x-transition.opacity.duration.200ms
            class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-line bg-raised px-4 py-3 text-sm shadow-lg shadow-black/10">
            <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full text-white"
                :class="toast.type === 'error' ? 'bg-accent' : 'bg-ok'">
                <svg x-show="toast.type !== 'error'" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                <svg x-show="toast.type === 'error'" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M12 7v6M12 17h.01"/></svg>
            </span>
            <p class="flex-1 text-ink" x-text="toast.message"></p>
            <template x-if="toast.action">
                <a :href="toast.action.url" class="link shrink-0 text-accent" x-text="toast.action.label"></a>
            </template>
            <button type="button" class="-m-1 p-1 text-muted hover:text-ink" @click="dismiss(toast.id)" aria-label="Dismiss">
                <x-icon name="x" :size="16"/>
            </button>
        </div>
    </template>
</div>
