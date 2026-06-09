@extends('admin.layouts.app')
@section('title', 'Người dùng')
@section('page-title', 'Người dùng')
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Người dùng</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Thêm người dùng</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th class="table-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roleLabel() }}</td>
                                <td><span class="badge {{ $user->isActive() ? 'badge-success' : 'badge-secondary' }}">{{ $user->isActive() ? 'Hoạt động' : 'Khóa' }}</span></td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-link btn-sm">Sửa</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Xóa?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-link-danger btn-sm">Xóa</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
