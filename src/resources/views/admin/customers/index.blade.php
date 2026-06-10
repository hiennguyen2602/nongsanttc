@extends('admin.layouts.app')

@section('title', 'Khách hàng')
@section('page-title', 'Khách hàng')

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
                            <th>Tên</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Đơn hàng</th>
                            <th>Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="whitespace-nowrap font-medium"><a href="{{ route('admin.customers.show', $customer) }}" class="admin-link hover:underline">{{ $customer->name }}</a></td>
                                <td class="whitespace-nowrap"><a href="{{ route('admin.customers.show', $customer) }}" class="admin-link hover:underline">{{ $customer->phone }}</a></td>
                                <td>{{ $customer->email ?? '—' }}</td>
                                <td>{{ $customer->orders_count }}</td>
                                <td class="whitespace-nowrap">{{ $customer->updated_at->format('d/m/Y H:i') }}</td>
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
