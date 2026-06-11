<header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-gent-topbar px-4 sm:px-6">
    <button
        type="button"
        @click="sidebarOpen = !sidebarOpen"
        class="rounded p-2 text-slate-600 hover:bg-white lg:hidden"
        aria-label="Toggle menu"
    >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <div class="min-w-0 flex-1">
        @hasSection('breadcrumbs')
            @yield('breadcrumbs')
        @endif
    </div>

    <div class="ml-auto flex items-center gap-2 sm:gap-4">
        <a href="{{ url('/') }}" target="_blank" class="admin-link hidden text-sm sm:inline">
            Xem website →
        </a>

        <div class="relative" x-data="{ open: false }">
            <button
                type="button"
                @click="open = !open"
                class="flex cursor-pointer items-center gap-2 rounded-lg bg-white px-3 py-1.5 text-sm shadow-sm ring-1 ring-slate-200"
            >
                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gent-accent text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </button>
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-52 rounded-lg bg-white py-1 shadow-lg ring-1 ring-slate-200"
                style="display: none;"
            >
                <div class="border-b border-slate-100 px-4 py-2">
                    <p class="truncate text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->roleLabel() }}</p>
                </div>
                <a href="{{ route('admin.password.change') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Đổi mật khẩu
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="block w-full cursor-pointer px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
