<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="admin-sidebar fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gent-sidebar text-slate-300 transition-transform duration-200 lg:translate-x-0"
>
    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gent-accent text-sm font-bold text-white">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-slate-400">{{ auth()->user()->roleLabel() }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <ul class="space-y-1">
            @foreach (config('admin.menu') as $item)
                <li>
                    @if (! empty($item['children']))
                        <details class="group" @if(collect($item['children'])->contains(fn ($c) => isset($c['route']) && $c['route'] && request()->routeIs($c['route']))) open @endif>
                            <summary class="admin-sidebar-link cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                                @include('admin.partials.icon', ['name' => $item['icon']])
                                <span class="flex-1">{{ $item['label'] }}</span>
                                <svg class="h-4 w-4 shrink-0 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            <ul class="mt-1 space-y-0.5 pl-4">
                                @foreach ($item['children'] as $child)
                                    @php $childActive = isset($child['route']) && $child['route'] && request()->routeIs($child['route']); @endphp
                                    <li>
                                        @if (! empty($child['route']))
                                            <a href="{{ route($child['route']) }}" class="admin-sidebar-sublink {{ $childActive ? 'is-active' : '' }}">
                                                {{ $child['label'] }}
                                            </a>
                                        @else
                                            <span class="admin-sidebar-sublink opacity-50">{{ $child['label'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @else
                        @php $active = isset($item['route']) && $item['route'] && request()->routeIs($item['route'] . '*'); @endphp
                        @if ($item['route'])
                            <a href="{{ route($item['route']) }}" class="admin-sidebar-link {{ $active ? 'is-active' : '' }}">
                                @include('admin.partials.icon', ['name' => $item['icon']])
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="border-t border-white/10 p-4">
        <a href="{{ route('admin.password.change') }}" class="mb-3 block text-xs text-slate-400 hover:text-white">Đổi mật khẩu</a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full rounded border border-white/20 px-3 py-2 text-xs text-slate-300 transition hover:bg-white/10 hover:text-white">
                Đăng xuất
            </button>
        </form>
    </div>
</aside>
