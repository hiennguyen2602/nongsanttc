@php
    $items = $items ?? [];
@endphp

@if (count($items) > 0)
    <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1 text-xs text-slate-500">
            @foreach ($items as $index => $item)
                @if ($index > 0)
                    <li aria-hidden="true" class="text-slate-400">/</li>
                @endif
                <li @class([
                    'min-w-0',
                    'max-w-[12rem] truncate sm:max-w-none' => $index === count($items) - 1,
                ])>
                    @if (! empty($item['url']) && $index < count($items) - 1)
                        <a href="{{ $item['url'] }}" class="admin-link hover:underline">{{ $item['label'] }}</a>
                    @else
                        <span @if ($index === count($items) - 1) aria-current="page" class="text-sm font-semibold text-black" @endif>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
