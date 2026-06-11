@extends('admin.layouts.app')

@section('title', 'Tin nhắn liên hệ')
@section('page-title', 'Tin nhắn liên hệ')
@section('breadcrumb', 'Liên hệ / Danh sách')

@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Danh sách tin nhắn</h2></div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="cell-text">Người gửi</th>
                            <th class="cell-text">Liên hệ</th>
                            <th class="cell-text">Chủ đề</th>
                            <th class="cell-text-wide">Nội dung</th>
                            <th class="cell-date">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $item)
                            <tr>
                                <td class="cell-text">
                                    <a href="{{ route('admin.contact-messages.show', $item) }}" class="admin-link font-medium hover:underline">
                                        {{ $item->name }}
                                    </a>
                                    @if ($item->isNew())
                                        <span class="ml-1 badge badge-warning">Mới</span>
                                    @endif
                                </td>
                                <td class="cell-text text-sm">
                                    <div>{{ $item->phone }}</div>
                                    @if ($item->email)
                                        <div class="text-slate-500">{{ $item->email }}</div>
                                    @endif
                                </td>
                                <td class="cell-text">{{ $item->subject ?: '—' }}</td>
                                <td class="cell-text-wide text-sm text-slate-600">
                                    <a href="{{ route('admin.contact-messages.show', $item) }}" class="admin-link hover:underline">
                                        {{ Str::limit($item->message, 100) }}
                                    </a>
                                </td>
                                <td class="cell-date text-sm">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400">Chưa có tin nhắn nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($messages->hasPages())
                <div class="mt-4">{{ $messages->links() }}</div>
            @endif
        </div>
    </div>
@endsection
