<div class="x_panel">
    <div class="x_title"><h2>{{ isset($user) ? 'Sửa người dùng' : 'Thêm người dùng' }}</h2></div>
    <div class="x_content">
        <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-form-narrow">
            @csrf @if(isset($user)) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Tên *</label>
                <input name="name" value="{{ old('name', $user->name ?? '') }}" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu {{ isset($user) ? '(để trống nếu không đổi)' : '*' }}</label>
                <input type="password" name="password" {{ isset($user) ? '' : 'required' }} autocomplete="new-password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Vai trò</label>
                <select name="type" class="form-select">
                    @foreach(\App\Models\User::roleLabels() as $val => $label)
                        <option value="{{ $val }}" @selected(old('type', $user->type ?? \App\Models\User::TYPE_STAFF)==$val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="1" @selected(old('status', $user->status ?? 1)==1)>Hoạt động</option>
                    <option value="0" @selected(old('status', $user->status ?? 1)==0)>Khóa</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
