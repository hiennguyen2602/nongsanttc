<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="x_panel">
                <div class="x_title"><h3>Thông tin sản phẩm</h3></div>
                <div class="x_content">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="mb-3 md:col-span-2">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control">
                        </div>
                        <div class="mb-3 md:col-span-2">
                            <label class="form-label">Mô tả ngắn</label>
                            <textarea name="short_description" rows="2" class="form-control">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                        </div>
                        <div class="mb-3 md:col-span-2">
                            <label class="form-label">Mô tả chi tiết</label>
                            @include('admin.partials.rich-editor', ['name' => 'description', 'value' => $product->description ?? ''])
                        </div>
                    </div>
                </div>
            </div>

            <div class="x_panel" x-data="{ variants: @js(old('variants', isset($product) ? $product->variants->map(fn($v) => ['flavor' => $v->flavor, 'size' => $v->size, 'price' => $v->price, 'sku' => $v->sku, 'stock' => $v->stock])->toArray() : [['flavor' => '', 'size' => '', 'price' => '', 'sku' => '', 'stock' => 0]])) }">
                <div class="x_title">
                    <h3>Biến thể</h3>
                    <button type="button" @click="variants.push({flavor:'',size:'',price:'',sku:'',stock:0})" class="btn btn-secondary btn-sm">+ Thêm biến thể</button>
                </div>
                <div class="x_content">
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="mb-3 grid grid-cols-2 gap-2 rounded border border-slate-200 bg-slate-50 p-3 md:grid-cols-5">
                            <input type="text" :name="'variants['+index+'][flavor]'" x-model="variant.flavor" placeholder="Vị" class="form-control">
                            <input type="text" :name="'variants['+index+'][size]'" x-model="variant.size" placeholder="Size" class="form-control">
                            <input type="number" :name="'variants['+index+'][price]'" x-model="variant.price" placeholder="Giá" class="form-control">
                            <input type="text" :name="'variants['+index+'][sku]'" x-model="variant.sku" placeholder="SKU" class="form-control">
                            <input type="number" :name="'variants['+index+'][stock]'" x-model="variant.stock" placeholder="Tồn" class="form-control">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="x_panel">
                <div class="x_title"><h3>Cài đặt</h3></div>
                <div class="x_content">
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-select">
                            <option value="">— Chọn —</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá gốc *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price ?? 0) }}" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá khuyến mãi</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tồn kho</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        @if (! empty($product?->image))
                            <img src="{{ store_media_url($product->image, 'medium') }}" alt="" class="mb-2 h-24 rounded object-cover ring-1 ring-slate-200">
                        @endif
                        <input type="file" name="image" accept="image/*" class="form-control">
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured" class="form-check-input" @checked(old('is_featured', $product->is_featured ?? false))>
                        <label for="is_featured" class="form-check-label">Sản phẩm nổi bật</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" @checked(old('is_active', $product->is_active ?? true))>
                        <label for="is_active" class="form-check-label">Hiển thị trên website</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">Lưu sản phẩm</button>
        </div>
    </div>
</form>
