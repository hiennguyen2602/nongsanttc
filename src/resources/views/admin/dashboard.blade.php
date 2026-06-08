@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Trang chủ / Dashboard')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-slate-500">Tổng quan hệ thống</p>
    </div>

    {{-- Stat tiles — Gentelella style --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                            <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-xs {{ $stat['trend'] === 'up' ? 'text-gent-accent' : 'text-slate-400' }}">
                                {{ $stat['change'] }}
                            </p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gent-accent/10 text-gent-accent">
                            @include('admin.partials.icon', ['name' => $stat['icon']])
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gent-accent"></div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Recent activity --}}
        <div class="xl:col-span-1">
            <div class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-slate-800">Hoạt động gần đây</h2>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentActivities as $activity)
                        <li class="px-5 py-4">
                            <p class="text-sm text-slate-700">{{ $activity['message'] }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $activity['time'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Recent orders table --}}
        <div class="xl:col-span-2">
            <div class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-slate-800">Đơn hàng gần đây</h2>
                    <span class="text-xs text-slate-400">Chưa có dữ liệu</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[480px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Mã đơn</th>
                                <th class="px-5 py-3">Khách hàng</th>
                                <th class="px-5 py-3">Số tiền</th>
                                <th class="px-5 py-3">Trạng thái</th>
                                <th class="px-5 py-3">Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr class="border-t border-slate-100">
                                    <td class="px-5 py-3">{{ $order['id'] }}</td>
                                    <td class="px-5 py-3">{{ $order['customer'] }}</td>
                                    <td class="px-5 py-3">{{ $order['amount'] }}</td>
                                    <td class="px-5 py-3">{{ $order['status'] }}</td>
                                    <td class="px-5 py-3">{{ $order['date'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                        Chưa có đơn hàng. Module đơn hàng sẽ được bổ sung sau.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick settings panel --}}
    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 font-semibold text-slate-800">Lưu trữ</h2>
            <div class="mb-2 flex justify-between text-sm">
                <span>Đã dùng</span>
                <span class="font-medium">0 GB / 8 GB</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full w-0 rounded-full bg-gent-accent"></div>
            </div>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 font-semibold text-slate-800">Cài đặt nhanh</h2>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center justify-between">
                    <span>Thông báo email</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Soon</span>
                </li>
                <li class="flex items-center justify-between">
                    <span>Xác thực 2 bước</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Soon</span>
                </li>
                <li class="flex items-center justify-between">
                    <span>Chế độ tối</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Soon</span>
                </li>
            </ul>
        </div>
    </div>
@endsection
