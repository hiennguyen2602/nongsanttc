@extends('admin.layouts.app')

@section('title', $post->title)
@section('page-title', 'Chi tiết bài viết')

@section('content')
    <div class="x_panel admin-post-panel">
        <div class="x_title x_title--actions-only">
            <div class="form-actions" style="margin:0">
                @if ($post->is_published)
                    <a href="{{ route('posts.show', $post->slug) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Xem trên web</a>
                @endif
                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary btn-sm">Sửa</a>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
            </div>
        </div>
        <div class="x_content">
            <div class="store-container store-container--article admin-post-show">
                <dl class="product-detail-list admin-post-detail">
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
                                    class="admin-post-featured-image w-full rounded-lg object-cover"
                                >
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="admin-post-article">
                    @include('partials.post-article-header', ['post' => $post])

                    @if ($post->content)
                        @include('partials.rich-content', ['html' => $post->content])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
