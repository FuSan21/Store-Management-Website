@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <p class="text-sm text-muted">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn-icon opacity-40" aria-hidden="true"><x-icon name="chevron-left" :size="18"/></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-icon" aria-label="Previous page"><x-icon name="chevron-left" :size="18"/></a>
            @endif
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-muted">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="hidden size-10 place-items-center rounded-lg bg-ink text-sm font-medium text-bg sm:grid">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="hidden size-10 place-items-center rounded-lg text-sm font-medium hover:bg-surface sm:grid">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-icon" aria-label="Next page"><x-icon name="chevron-right" :size="18"/></a>
            @else
                <span class="btn-icon opacity-40" aria-hidden="true"><x-icon name="chevron-right" :size="18"/></span>
            @endif
        </div>
    </nav>
@endif
