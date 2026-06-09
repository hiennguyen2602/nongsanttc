@php
    $existingImages = collect();
    if (! empty($product?->image)) {
        $existingImages->push(['path' => $product->image, 'url' => store_media_url($product->image, 'thumbnail')]);
    }
    foreach ((array) ($product?->gallery ?? []) as $galleryImage) {
        if (! empty($galleryImage)) {
            $existingImages->push(['path' => $galleryImage, 'url' => store_media_url($galleryImage, 'thumbnail')]);
        }
    }
    $existingImages = $existingImages->values();
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="x_panel">
                <div class="x_title"><h3>Thông tin sản phẩm</h3></div>
                <div class="x_content">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mã sản phẩm</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? ($suggestedSku ?? '')) }}" class="form-control">
                        <p class="mt-1 text-xs text-slate-500">Mã được tạo tự động và duy nhất, bạn có thể sửa lại.</p>
                    </div>

                    <div class="mb-3" x-data="productImages(@js($existingImages))">
                        <label class="form-label">Hình ảnh sản phẩm *</label>
                        <input type="file" x-ref="fileInput" name="images[]" accept="image/*" multiple @change="addFiles($event)" class="form-control">
                        <p class="mt-1 text-xs text-slate-500">Có thể chọn nhiều ảnh cùng lúc. Chọn "Ảnh chính" để làm ảnh đại diện hiển thị ở danh sách.</p>
                        @error('images')<p class="field-error">{{ $message }}</p>@enderror
                        @foreach ($errors->get('images.*') as $imageErrors)
                            @foreach ($imageErrors as $imageError)
                                <p class="field-error">{{ $imageError }}</p>
                            @endforeach
                        @endforeach

                        <input type="hidden" name="main_selector" :value="mainPayload">
                        <template x-for="item in existing" :key="'hidden-' + item.path">
                            <input type="hidden" name="existing_images[]" :value="item.path">
                        </template>

                        <div class="image-grid" x-show="existing.length || newImages.length" style="display:none">
                            <template x-for="item in existing" :key="'e-' + item.path">
                                <div class="image-card" :class="{ 'is-main': isMainExisting(item.path) }">
                                    <span class="image-main-badge" x-show="isMainExisting(item.path)">Ảnh chính</span>
                                    <img :src="item.url" alt="">
                                    <div class="image-card-bar">
                                        <label>
                                            <input type="radio" name="main_radio" :checked="isMainExisting(item.path)" @change="setMainExisting(item.path)"> Ảnh chính
                                        </label>
                                        <button type="button" class="image-card-remove" @click="removeExisting(item.path)">Xóa</button>
                                    </div>
                                </div>
                            </template>
                            <template x-for="item in newImages" :key="'n-' + item.id">
                                <div class="image-card" :class="{ 'is-main': isMainNew(item.id) }">
                                    <span class="image-main-badge" x-show="isMainNew(item.id)">Ảnh chính</span>
                                    <img :src="item.url" alt="">
                                    <div class="image-card-bar">
                                        <label>
                                            <input type="radio" name="main_radio" :checked="isMainNew(item.id)" @change="setMainNew(item.id)"> Ảnh chính
                                        </label>
                                        <button type="button" class="image-card-remove" @click="removeNew(item.id)">Xóa</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="short_description" rows="10" class="form-control">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Mô tả chi tiết</label>
                        @include('admin.partials.rich-editor', ['name' => 'description', 'value' => $product->description ?? ''])
                    </div>
                </div>
            </div>

            <div class="x_panel" x-data="{ variants: @js(old('variants', isset($product) ? $product->variants->map(fn($v) => ['id' => $v->id, 'flavor' => $v->flavor, 'size' => $v->size, 'price' => $v->price, 'sku' => $v->sku, 'stock' => $v->stock])->toArray() : [['id' => '', 'flavor' => '', 'size' => '', 'price' => '', 'sku' => '', 'stock' => 0]])) }">
                <div class="x_title">
                    <h3>Biến thể</h3>
                    <button type="button" @click="variants.push({id:'',flavor:'',size:'',price:'',sku:'',stock:0})" class="btn btn-secondary btn-sm">+ Thêm biến thể</button>
                </div>
                <div class="x_content">
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="mb-3 grid grid-cols-2 gap-2 rounded border border-slate-200 bg-slate-50 p-3 md:grid-cols-5">
                            <input type="hidden" :name="'variants['+index+'][id]'" :value="variant.id">
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
                        <input type="text" inputmode="numeric" name="price" value="{{ old('price', $product->price ?? 0) }}" required class="form-control input-number">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá khuyến mãi</label>
                        <input type="text" inputmode="numeric" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control input-number">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tồn kho</label>
                        <input type="text" inputmode="numeric" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control input-number">
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

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </div>
    </div>
</form>
