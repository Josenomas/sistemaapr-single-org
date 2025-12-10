@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de páginas" style="display: flex; align-items: center; gap: 8px; font-size: 0.875rem;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 8px 12px; color: #9ca3af; cursor: not-allowed;">
                <svg style="width: 16px; height: 16px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding: 8px 12px; color: #3b82f6; text-decoration: none; border-radius: 6px; transition: all 0.2s; display: flex; align-items: center; gap: 4px;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="padding: 8px 12px; color: #9ca3af;">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="min-width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: #3b82f6; color: white; border-radius: 6px; font-weight: 600;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="min-width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; color: #4b5563; text-decoration: none; border-radius: 6px; transition: all 0.2s; font-weight: 500;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding: 8px 12px; color: #3b82f6; text-decoration: none; border-radius: 6px; transition: all 0.2s; display: flex; align-items: center; gap: 4px;">
                Siguiente
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span style="padding: 8px 12px; color: #9ca3af; cursor: not-allowed;">
                Siguiente
                <svg style="width: 16px; height: 16px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif

        {{-- Results Info --}}
        <span style="margin-left: 12px; color: #6b7280;">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </span>
    </nav>
@endif
