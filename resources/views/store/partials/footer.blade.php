<footer id="lien-he" class="mt-auto bg-slate-100 text-slate-600">
    <div class="store-container py-10 sm:py-12 lg:py-12">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
            {{-- Brand --}}
            <div>
                <div class="mb-4 flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded bg-brand text-sm font-bold text-white">TTC</div>
                    <span class="text-lg font-bold text-slate-800">{{ store_setting('name') }}</span>
                </div>
                <ul class="space-y-2 text-sm">
                    @if (store_setting('company_name'))
                        <li class="font-semibold text-slate-800">{{ store_setting('company_name') }}</li>
                    @endif
                    @if (store_setting('address'))
                        <li>{{ store_setting('address') }}</li>
                    @endif
                    <li>Điện thoại: <a href="tel:{{ preg_replace('/\s+/', '', store_setting('phone')) }}" class="text-brand hover:underline">{{ store_setting('phone') }}</a></li>
                    <li>Email: <a href="mailto:{{ store_setting('email') }}" class="text-brand hover:underline">{{ store_setting('email') }}</a></li>
                </ul>
            </div>

            {{-- Useful links --}}
            <div>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-800">Liên kết</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-brand">Về chúng tôi</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-brand">Sản phẩm</a></li>
                    <li><a href="{{ route('posts.index') }}" class="hover:text-brand">Tin tức</a></li>
                    <li><a href="#" class="hover:text-brand">Chính sách giao hàng</a></li>
                    <li><a href="#" class="hover:text-brand">Chính sách bảo mật</a></li>
                </ul>
            </div>

            {{-- Categories --}}
            <div>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-800">Danh mục</h3>
                <ul class="space-y-2 text-sm">
                    @forelse ($footerCategories as $category)
                        <li>
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="hover:text-brand">
                                {{ $category->name }}
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-400">Chưa có danh mục</li>
                    @endforelse
                </ul>
            </div>

            {{-- Newsletter — Ogani style --}}
            <div class="footer-newsletter min-w-0">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-800">Đăng ký nhận tin</h3>
                <p class="mb-4 text-sm">Nhận thông tin khuyến mãi và sản phẩm mới qua email.</p>
                <form class="flex flex-nowrap items-stretch gap-2" onsubmit="return false;">
                    <input type="email" placeholder="Email của bạn" class="min-w-0 flex-1 rounded border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand">
                    <button type="submit" class="shrink-0 whitespace-nowrap rounded bg-brand px-4 py-2 text-sm font-semibold uppercase text-white hover:bg-brand-dark">Đăng ký</button>
                </form>
                <div class="mt-4 flex flex-wrap gap-2">
                    @if (store_setting('facebook'))
                    @include('store.partials.social-icon-link', [
                        'platform' => 'facebook',
                        'href' => store_setting('facebook'),
                        'label' => 'Facebook',
                    ])
                    @endif
                    @if (store_setting('zalo'))
                    @include('store.partials.social-icon-link', [
                        'platform' => 'zalo',
                        'href' => store_setting('zalo'),
                        'label' => 'Zalo',
                    ])
                    @endif
                    @if (store_setting('youtube'))
                    <a href="{{ store_setting('youtube') }}" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-600 shadow-sm ring-1 ring-slate-200 hover:text-brand" aria-label="Youtube">YT</a>
                    @endif
                    @if (store_setting('tiktok'))
                    <a href="{{ store_setting('tiktok') }}" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-600 shadow-sm ring-1 ring-slate-200 hover:text-brand" aria-label="TikTok">TT</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 bg-white py-4">
        <div class="store-container text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} {{ store_setting('name') }}. All rights reserved.
        </div>
    </div>
</footer>
