@extends('admin.layouts.app')
@section('title', 'Khuyến mãi')
@section('page-title', 'Khuyến mãi')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Khuyến mãi'],
    ])])
@endsection
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Khuyến mãi</h2>
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">+ Thêm mã</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th class="cell-code">Mã</th><th class="cell-text-wide">Tiêu đề</th><th class="cell-price">ĐH tối thiểu</th><th class="cell-price">Giảm</th><th class="table-actions"></th></tr></thead>
                    <tbody>
                        @foreach($promotions as $promo)
                            <tr>
                                <td class="cell-code font-mono font-medium">{{ $promo->code }}</td>
                                <td class="cell-text-wide">{{ $promo->title }}</td>
                                <td class="cell-price">{{ format_money($promo->min_order) }}</td>
                                <td class="cell-price">{{ format_money($promo->discount_amount) }}</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.promotions.edit', $promo) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.promotions.destroy', $promo) }}" class="inline" data-confirm-entity="mã khuyến mãi" data-confirm-name="{{ $promo->code }}">@csrf @method('DELETE')<button type="button" class="btn btn-link btn-link-danger btn-sm" data-confirm-trigger>Xóa</button></form>
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
