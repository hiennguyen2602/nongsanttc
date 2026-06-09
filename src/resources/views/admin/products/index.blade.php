@extends('admin.layouts.app')

@section('title', 'Sản phẩm')
@section('page-title', 'Danh sách sản phẩm')

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
                            <th>Ảnh</th>
                            <th>Tên</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th class="table-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    @if ($product->image)
                                        <img src="{{ store_media_url($product->image, 'thumbnail') }}" alt="" class="h-12 w-12 rounded object-cover ring-1 ring-slate-200">
                                    @endif
                                </td>
                                <td class="font-medium"><a href="{{ route('admin.products.show', $product) }}" class="text-[#015096] hover:underline">{{ $product->name }}</a></td>
                                <td>{{ $product->category?->name ?? '—' }}</td>
                                <td>{{ $product->formattedPrice() }}</td>
                                <td>
                                    <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $product->is_active ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
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
