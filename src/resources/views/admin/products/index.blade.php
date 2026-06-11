@extends('admin.layouts.app')

@section('title', 'Sản phẩm')
@section('page-title', 'Danh sách sản phẩm')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Danh sách sản phẩm'],
    ])])
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Sản phẩm</h2>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Thêm sản phẩm</a>
        </div>
        <div class="x_content">
            <form method="GET" class="admin-toolbar">
                <div class="admin-search">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tìm sản phẩm..." class="form-control">
                    <button type="submit" class="btn btn-secondary btn-sm">Tìm</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="cell-image">Ảnh</th>
                            <th class="cell-text-wide">Tên</th>
                            <th class="cell-text">Danh mục</th>
                            <th class="cell-price">Giá</th>
                            <th class="cell-status">Trạng thái</th>
                            <th class="table-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="cell-image">
                                    @if ($product->image)
                                        <img src="{{ store_media_url($product->image, 'thumbnail') }}" alt="" class="h-12 w-20 max-w-none shrink-0 rounded object-cover ring-1 ring-slate-200">
                                    @endif
                                </td>
                                <td class="cell-text-wide font-medium"><a href="{{ route('admin.products.show', $product) }}" class="admin-link hover:underline">{{ $product->name }}</a></td>
                                <td class="cell-text">{{ $product->category?->name ?? '—' }}</td>
                                <td class="cell-price">{{ $product->formattedPrice() }}</td>
                                <td class="cell-status">
                                    @include('admin.partials.status-badge', ['label' => $product->visibilityLabel(), 'class' => $product->visibilityBadgeClass()])
                                </td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Xóa sản phẩm?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-link-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
@endsection
