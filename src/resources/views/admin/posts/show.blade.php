@extends('admin.layouts.app')

@section('title', $post->title)
@section('page-title', 'Chi tiết bài viết')

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>{{ $post->title }}</h2>
            <div class="form-actions" style="margin:0">
                @if ($post->is_published)
                    <a href="{{ route('posts.show', $post->slug) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Xem trên web</a>
                @endif
                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary btn-sm">Sửa</a>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
            </div>
        </div>
        <div class="x_content">
            <dl class="product-detail-list admin-post-detail">
                <div>
                    <dt>Ngày đăng</dt>
                    <dd>{{ $post->published_at?->format('d/m/Y') ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Trạng thái</dt>
                    <dd>
                        @include('admin.partials.status-badge', ['label' => $post->publishStatusLabel(), 'class' => $post->publishStatusBadgeClass()])
                    </dd>
                </div>
                <div>
                    <dt>Ảnh đại diện</dt>
                    <dd>
                        @if ($post->image)
                            <img
                                src="{{ store_media_url($post->image, 'medium') }}"
                                alt=""
                                class="admin-post-featured-image w-full rounded-lg object-cover ring-1 ring-slate-200"
                            >
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Tóm tắt</dt>
                    <dd class="whitespace-pre-wrap text-slate-600">{{ $post->excerpt ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Nội dung</dt>
                    <dd>
                        @if ($post->content)
                            <div class="prose prose-slate max-w-none text-slate-700">{!! $post->content !!}</div>
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
