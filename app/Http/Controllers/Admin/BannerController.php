<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
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

    public function store(StoreBannerRequest $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $request->toModelData();

        $data['image'] = $uploader->upload(
            $request->file('image'),
            'uploads/banners/' . date('Y/m'),
            null,
            (int) config('media.banner_desktop_max_width', 1200),
        )['path'];

        if ($request->hasFile('image_mobile')) {
            $data['image_mobile'] = $uploader->upload(
                $request->file('image_mobile'),
                'uploads/banners/' . date('Y/m'),
                null,
                (int) config('media.banner_mobile_max_width', 768),
            )['path'];
        }

        Banner::query()->create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner, ImageUploadService $uploader): RedirectResponse
    {
        $data = $request->toModelData();

        $data['image'] = $this->handleBannerImage(
            $request,
            $uploader,
            $banner,
            'image',
            'existing_image',
            (int) config('media.banner_desktop_max_width', 1200),
        );

        if ($request->hasFile('image_mobile') || $request->filled('existing_image_mobile') || $banner->image_mobile) {
            $data['image_mobile'] = $this->handleBannerImage(
                $request,
                $uploader,
                $banner,
                'image_mobile',
                'existing_image_mobile',
                (int) config('media.banner_mobile_max_width', 768),
                optional: true,
            );
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

    private function handleBannerImage(
        UpdateBannerRequest|StoreBannerRequest|Request $request,
        ImageUploadService $uploader,
        Banner $banner,
        string $field,
        string $existingField,
        int $maxWidth,
        bool $optional = false,
    ): ?string {
        $current = $banner->{$field};

        if ($request->hasFile($field)) {
            $uploader->delete($current);

            return $uploader->upload(
                $request->file($field),
                'uploads/banners/' . date('Y/m'),
                null,
                $maxWidth,
            )['path'];
        }

        $kept = resolve_kept_upload_path(
            $request->input($existingField),
            $current,
            'uploads/banners',
            $field,
        );

        if ($kept !== null) {
            return $kept;
        }

        if ($optional) {
            if ($current) {
                $uploader->delete($current);
            }

            return null;
        }

        return $current;
    }
}
