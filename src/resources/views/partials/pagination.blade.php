@if ($paginator->hasPages())
    @php
        $last = $paginator->lastPage();
        $cur = $paginator->currentPage();
        $windowSize = 5;

        if ($last <= $windowSize) {
            $start = 1;
            $end = $last;
            $showLeftEllipsis = false;
            $showRightEllipsis = false;
        } else {
            $half = intdiv($windowSize - 1, 2);
            $start = max(1, min($cur - $half, $last - $windowSize + 1));
            $end = min($last, $start + $windowSize - 1);
            if ($end - $start < $windowSize - 1) {
                $start = max(1, $end - $windowSize + 1);
            }
            $showLeftEllipsis = $start > 1;
            $showRightEllipsis = $end < $last;
        }
    @endphp
    <nav aria-label="Phân trang" class="pagination-window-nav d-flex justify-content-end w-100">
        <ul class="pagination pagination-sm mb-0 flex-wrap justify-content-end">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link user-select-none" aria-hidden="true" tabindex="-1">«</span>
                @else
                    <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Trang đầu">«</a>
                @endif
            </li>

            @if ($showLeftEllipsis)
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-transparent px-1 px-sm-2 user-select-none" aria-hidden="true">…</span>
                </li>
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $cur)
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            @if ($showRightEllipsis)
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-transparent px-1 px-sm-2 user-select-none" aria-hidden="true">…</span>
                </li>
            @endif

            <li class="page-item {{ $cur >= $last ? 'disabled' : '' }}">
                @if ($cur >= $last)
                    <span class="page-link user-select-none" aria-hidden="true" tabindex="-1">»</span>
                @else
                    <a class="page-link" href="{{ $paginator->url($last) }}" aria-label="Trang cuối">»</a>
                @endif
            </li>
        </ul>
    </nav>
@endif
