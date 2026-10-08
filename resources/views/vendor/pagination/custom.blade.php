@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="pagination-container">
        {{-- Info pagination --}}
        <div class="pagination-info">
            Menampilkan <strong>{{ $paginator->firstItem() }}</strong> sampai <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> log aktivitas
        </div>

        {{-- Pagination Controls --}}
        <ul class="pagination-list">
            {{-- Tombol Halaman Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item">
                    <span class="pagination-link disabled" aria-disabled="true" aria-label="Sebelumnya">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link" aria-label="Sebelumnya">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Pemisah Tiga Titik (...) --}}
                @if (is_string($element))
                    <li class="pagination-item"><span class="pagination-ellipsis">{{ $element }}</span></li>
                @endif

                {{-- Kumpulan Link Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item">
                                <span class="pagination-link active" aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Halaman Berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-link" aria-label="Berikutnya">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>
            @else
                <li class="pagination-item">
                    <span class="pagination-link disabled" aria-disabled="true" aria-label="Berikutnya">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@elseif($paginator->total() > 0)
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan <strong>{{ $paginator->firstItem() }}</strong> sampai <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> log aktivitas
        </div>
    </div>
@endif
