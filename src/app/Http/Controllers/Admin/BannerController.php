<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        $this->ensureDesktopImage($request);

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

    public function update(Request $request, Banner $banner, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request);
        $this->ensureDesktopImageOnUpdate($request, $banner);

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

    private function validated(Request $request): array
    {
        $maxMb = (float) config('media.max_image_mb', 5);
        $maxKb = (int) round($maxMb * 1024);
        $maxLabel = rtrim(rtrim(number_format($maxMb, 1), '0'), '.');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:500'],
            'position' => ['required', 'string', 'in:home_cta'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:' . $maxKb],
            'image_mobile' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:' . $maxKb],
        ], [
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh desktop phải có định dạng: jpeg, jpg, png, webp, gif.',
            'image.max' => 'Ảnh desktop không được vượt quá ' . $maxLabel . 'MB.',
            'image_mobile.image' => 'File tải lên phải là hình ảnh.',
            'image_mobile.mimes' => 'Ảnh mobile phải có định dạng: jpeg, jpg, png, webp, gif.',
            'image_mobile.max' => 'Ảnh mobile không được vượt quá ' . $maxLabel . 'MB.',
            'position.in' => 'Vị trí banner không hợp lệ.',
        ]);

        unset($data['image'], $data['image_mobile']);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function ensureDesktopImage(Request $request): void
    {
        if (! $request->hasFile('image')) {
            throw ValidationException::withMessages([
                'image' => 'Vui lòng chọn ảnh desktop.',
            ]);
        }
    }

    private function ensureDesktopImageOnUpdate(Request $request, Banner $banner): void
    {
        if ($request->hasFile('image') || filled($request->input('existing_image'))) {
            return;
        }

        throw ValidationException::withMessages([
            'image' => 'Vui lòng chọn ảnh desktop.',
        ]);
    }

    private function handleBannerImage(
        Request $request,
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

        $kept = (string) $request->input($existingField);

        if ($kept !== '') {
            if ($current && $kept !== $current) {
                $uploader->delete($current);
            }

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
