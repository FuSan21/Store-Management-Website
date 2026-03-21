@props(['status'])
@php
    $tones = [
        'amber' => 'bg-warn-soft text-warn',
        'blue' => 'bg-info-soft text-info',
        'sky' => 'bg-info-soft text-info',
        'green' => 'bg-ok-soft text-ok',
        'neutral' => 'bg-surface text-muted',
        'red' => 'bg-accent-soft text-accent',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap '.$tones[$status->tone()]]) }}>
    <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $status->label() }}
</span>
