@props(['rating' => 0, 'count' => null, 'size' => 14])
@php($rounded = round((float) $rating * 2) / 2)
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }}>
    <span class="inline-flex text-ink" role="img" aria-label="{{ number_format((float) $rating, 1) }} out of 5 stars">
        @for ($i = 1; $i <= 5; $i++)
            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" aria-hidden="true">
                <defs>
                    <linearGradient id="half-{{ $i }}-{{ $size }}"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="transparent"/></linearGradient>
                </defs>
                <path d="M11.52 2.3a.53.53 0 0 1 .95 0l2.31 4.68a2.12 2.12 0 0 0 1.6 1.16l5.16.76a.53.53 0 0 1 .3.9l-3.74 3.64a2.12 2.12 0 0 0-.61 1.88l.88 5.14a.53.53 0 0 1-.77.56l-4.62-2.43a2.12 2.12 0 0 0-1.97 0L6.4 21.01a.53.53 0 0 1-.77-.56l.88-5.13a2.12 2.12 0 0 0-.61-1.88L2.16 9.8a.53.53 0 0 1 .29-.9l5.17-.76a2.12 2.12 0 0 0 1.6-1.16z"
                    fill="{{ $rounded >= $i ? 'currentColor' : ($rounded >= $i - 0.5 ? 'url(#half-'.$i.'-'.$size.')' : 'transparent') }}"
                    stroke="currentColor" stroke-width="1.6"/>
            </svg>
        @endfor
    </span>
    @if ($count !== null)
        <span class="text-xs text-muted">{{ $count > 0 ? number_format((float) $rating, 1).' · '.$count : 'No reviews yet' }}</span>
    @endif
</span>
