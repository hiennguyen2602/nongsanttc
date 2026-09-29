<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'nextSortOrder' => (Category::query()->max('sort_order') ?? 0) + 1,
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::query()->create($request->toModelData());

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->toModelData());

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}
