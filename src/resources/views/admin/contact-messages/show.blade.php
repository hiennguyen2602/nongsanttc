@extends('admin.layouts.app')

@section('title', $message->name . ' — Tin liên hệ')
@section('page-title', 'Chi tiết tin liên hệ')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Tin nhắn liên hệ', 'url' => route('admin.contact-messages.index')],
        ['label' => 'Chi tiết tin liên hệ'],
    ])])
@endsection

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <div class="flex flex-wrap items-center gap-2">
                <h2>Chi tiết tin liên hệ</h2>
                @if ($message->isNew())
                    <span class="badge badge-warning">Chưa đọc</span>
                @endif
            </div>
            <div class="form-actions" style="margin:0">
                <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" class="inline" onsubmit="return confirm('Xóa tin nhắn này?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </div>
        </div>

        <div class="x_content">
            <dl class="product-detail-list">
                <div>
                    <dt>Họ tên</dt>
                    <dd>{{ $message->name ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Số điện thoại</dt>
                    <dd>
                        @if ($message->phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $message->phone) }}" class="admin-link hover:underline">{{ $message->phone }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Email</dt>
                    <dd>
                        @if ($message->email)
                            <a href="mailto:{{ $message->email }}" class="admin-link hover:underline">{{ $message->email }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Chủ đề</dt>
                    <dd>{{ $message->subject ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Nội dung</dt>
                    <dd class="whitespace-pre-wrap">{{ $message->message ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Thời gian gửi</dt>
                    <dd>{{ $message->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
