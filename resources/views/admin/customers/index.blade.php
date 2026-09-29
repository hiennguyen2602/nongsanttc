@extends('admin.layouts.app')

@section('title', 'Khách hàng')
@section('page-title', 'Khách hàng')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Khách hàng'],
    ])])
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Khách hàng</h2></div>
        <div class="x_content">
            <form method="GET" class="admin-toolbar">
                <div class="admin-search">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tên, SĐT, email..." class="form-control">
                    <button type="submit" class="btn btn-secondary btn-sm">Tìm</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="cell-text">Tên</th>
                            <th class="cell-phone">Số điện thoại</th>
                            <th class="cell-text">Email</th>
                            <th class="cell-num">Đơn hàng</th>
                            <th class="cell-date">Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="cell-text font-medium"><a href="{{ route('admin.customers.show', $customer) }}" class="admin-link hover:underline">{{ $customer->name }}</a></td>
                                <td class="cell-phone"><a href="{{ route('admin.customers.show', $customer) }}" class="admin-link hover:underline">{{ $customer->phone }}</a></td>
                                <td class="cell-text">{{ $customer->email ?? '—' }}</td>
                                <td class="cell-num">{{ $customer->orders_count }}</td>
                                <td class="cell-date">{{ $customer->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-slate-500">Chưa có khách hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $customers->links() }}</div>
        </div>
    </div>
@endsection
