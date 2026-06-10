<div class="x_panel">
    <div class="x_title"><h2>{{ isset($promotion) ? 'Sửa khuyến mãi' : 'Thêm khuyến mãi' }}</h2></div>
    <div class="x_content">
        <form method="POST" action="{{ isset($promotion) ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" class="admin-form-narrow">
            @csrf @if(isset($promotion)) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Mã *</label><input name="code" value="{{ old('code', $promotion->code ?? '') }}" required class="form-control uppercase"></div>
            <div class="mb-3"><label class="form-label">Tiêu đề *</label><input name="title" value="{{ old('title', $promotion->title ?? '') }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">Mô tả</label><input name="description" value="{{ old('description', $promotion->description ?? '') }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Đơn tối thiểu</label><input type="text" inputmode="numeric" name="min_order" value="{{ old('min_order', $promotion->min_order ?? 0) }}" class="form-control input-number"></div>
            <div class="mb-3"><label class="form-label">Số tiền giảm</label><input type="text" inputmode="numeric" name="discount_amount" value="{{ old('discount_amount', $promotion->discount_amount ?? 0) }}" class="form-control input-number"></div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" id="promo_active" class="form-check-input" @checked(old('is_active', $promotion->is_active ?? true))>
                <label for="promo_active" class="form-check-label">Kích hoạt</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
