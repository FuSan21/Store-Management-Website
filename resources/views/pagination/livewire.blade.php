@php($pageName = $paginator->getPageName())
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <p class="text-sm text-muted">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>
        <div class="flex items-center gap-1">
            <button type="button" class="btn-icon" wire:click="previousPage('{{ $pageName }}')" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })" @disabled($paginator->onFirstPage()) aria-label="Previous page">
                <x-icon name="chevron-left" :size="18"/>
            </button>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-muted">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <button type="button" wire:key="page-{{ $page }}" wire:click="gotoPage({{ $page }}, '{{ $pageName }}')" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                            @class(['hidden size-10 rounded-lg text-sm font-medium sm:inline-block', 'bg-ink text-bg' => $page == $paginator->currentPage(), 'hover:bg-surface' => $page != $paginator->currentPage()])
                            @if ($page == $paginator->currentPage()) aria-current="page" @endif>{{ $page }}</button>
                    @endforeach
                @endif
            @endforeach
            <button type="button" class="btn-icon" wire:click="nextPage('{{ $pageName }}')" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })" @disabled(! $paginator->hasMorePages()) aria-label="Next page">
                <x-icon name="chevron-right" :size="18"/>
            </button>
        </div>
    </nav>
@endif
