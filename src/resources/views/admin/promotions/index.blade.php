@extends('admin.layouts.app')
@section('title', 'Khuyến mãi')
@section('page-title', 'Khuyến mãi')
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Khuyến mãi</h2>
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">+ Thêm mã</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Mã</th><th>Tiêu đề</th><th>ĐH tối thiểu</th><th>Giảm</th><th class="table-actions"></th></tr></thead>
                    <tbody>
                        @foreach($promotions as $promo)
                            <tr>
                                <td class="font-mono font-medium">{{ $promo->code }}</td>
                                <td>{{ $promo->title }}</td>
                                <td>{{ format_money($promo->min_order) }}</td>
                                <td>{{ format_money($promo->discount_amount) }}</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.promotions.edit', $promo) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.promotions.destroy', $promo) }}" class="inline" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')<button type="submit" class="btn btn-link btn-link-danger btn-sm">Xóa</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $promotions->links() }}</div>
        </div>
    </div>
@endsection
