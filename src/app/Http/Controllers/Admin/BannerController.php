<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => Banner::orderBy('sort_order')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $uploader->upload($request->file('image'), 'uploads/banners/' . date('Y/m'), 'banner')['path'];
        }

        if ($request->hasFile('image_mobile')) {
            $data['image_mobile'] = $uploader->upload($request->file('image_mobile'), 'uploads/banners/' . date('Y/m'), 'hero_mobile')['path'];
        }

        Banner::query()->create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $uploader->delete($banner->image);
            $data['image'] = $uploader->upload($request->file('image'), 'uploads/banners/' . date('Y/m'), 'banner')['path'];
        }

        if ($request->hasFile('image_mobile')) {
            $uploader->delete($banner->image_mobile);
            $data['image_mobile'] = $uploader->upload($request->file('image_mobile'), 'uploads/banners/' . date('Y/m'), 'hero_mobile')['path'];
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công.');
    }

    public function destroy(Banner $banner, ImageUploadService $uploader): RedirectResponse
    {
        $uploader->delete($banner->image);
        $uploader->delete($banner->image_mobile);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Xóa banner thành công.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:500'],
            'position' => ['required', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_mobile' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
