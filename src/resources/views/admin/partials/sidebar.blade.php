<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gent-sidebar text-slate-300 transition-transform duration-200 lg:translate-x-0"
>
    <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded bg-gent-accent text-sm font-bold text-white">
            TTC
        </div>
        <div>
            <p class="text-sm font-semibold text-white">{{ config('admin.name') }}</p>
            <p class="text-xs text-slate-400">Admin Panel</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <ul class="space-y-1">
            @foreach (config('admin.menu') as $item)
                <li>
                    @if (! empty($item['children']))
                        <details class="group">
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded px-3 py-2.5 text-sm hover:bg-gent-sidebar-hover hover:text-white">
                                @include('admin.partials.icon', ['name' => $item['icon']])
                                <span class="flex-1">{{ $item['label'] }}</span>
                                <svg class="h-4 w-4 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <ul class="mt-1 space-y-1 pl-9">
                                @foreach ($item['children'] as $child)
                                    <li>
                                        <span class="block rounded px-3 py-2 text-xs text-slate-400 hover:text-white">
                                            {{ $child['label'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @else
                        @php
                            $active = isset($item['route']) && $item['route'] && request()->routeIs($item['route']);
                        @endphp
                        @if ($item['route'])
                            <a
                                href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 rounded px-3 py-2.5 text-sm transition {{ $active ? 'bg-gent-sidebar-hover text-white' : 'hover:bg-gent-sidebar-hover hover:text-white' }}"
                            >
                                @include('admin.partials.icon', ['name' => $item['icon']])
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="flex items-center gap-3 rounded px-3 py-2.5 text-sm text-slate-500">
                                @include('admin.partials.icon', ['name' => $item['icon']])
                                {{ $item['label'] }}
                                <span class="ml-auto rounded bg-white/10 px-1.5 py-0.5 text-[10px]">Soon</span>
                            </span>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gent-accent text-xs font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
