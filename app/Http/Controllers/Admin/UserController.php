<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->whereIn('type', [User::TYPE_ADMIN, User::TYPE_STAFF])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công.');
    }

    public function edit(User $user): View
    {
        $this->ensureManagedUser($user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->ensureManagedUser($user);

        $user->update($request->toModelData());

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureManagedUser($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Không thể xóa tài khoản đang đăng nhập.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }

    /** Chỉ cho phép thao tác tài khoản Admin / Staff (không phải khách type=3). */
    private function ensureManagedUser(User $user): void
    {
        if (! in_array((int) $user->type, [User::TYPE_ADMIN, User::TYPE_STAFF], true)) {
            throw new NotFoundHttpException();
        }
    }
}
