@props(['label', 'value', 'delta' => null, 'hint' => null, 'invert' => false])
<div {{ $attributes->merge(['class' => 'card p-5']) }}>
    <dt class="text-sm text-muted">{{ $label }}</dt>
    <dd class="mt-2 text-2xl font-semibold tabular-nums sm:text-3xl">{{ $value }}</dd>
    @if ($delta !== null)
        @php($good = $invert ? $delta <= 0 : $delta >= 0)
        <dd class="mt-2 flex items-center gap-1 text-xs">
            <span @class(['inline-flex items-center gap-0.5 font-semibold', 'text-ok' => $good, 'text-accent' => ! $good])>
                <x-icon :name="$delta >= 0 ? 'trending-up' : 'trending-down'" :size="14"/>
                {{ $delta >= 0 ? '+' : '' }}{{ $delta }}%
            </span>
            <span class="text-muted">vs previous period</span>
        </dd>
    @elseif ($hint)
        <dd class="mt-2 text-xs text-muted">{{ $hint }}</dd>
    @endif
</div>
