# Quy ước phát triển

## Database migrations

Dự án đang trong giai đoạn phát triển. **Trước khi lên production**, sẽ squash/xóa các migration thừa, chỉ giữ migration gốc gọn.

### Khi thêm/sửa schema

1. **Luôn cập nhật migration bảng gốc** (`database/migrations/*_create_*_tables.php` hoặc file `create` tương ứng) để `migrate:fresh` trên môi trường mới có đủ cột/bảng ngay từ đầu.

2. **Nếu vẫn tạo migration bổ sung** (DB dev đã chạy migration cũ):
   - Trong `up()`: dùng `Schema::hasTable()` / `Schema::hasColumn()` trước khi `create` / `add`.
   - Trong `down()`: check tồn tại trước khi `drop`.
   - Comment đầu file: ghi rõ cột/bảng đã gộp vào migration gốc nào.

3. **Trước production** (checklist):
   - Gộp mọi thay đổi schema vào migration `create` gốc.
   - Xóa các file migration bổ sung đã squash.
   - Chạy `migrate:fresh --seed` trên staging để xác nhận.

### Ví dụ

- Cột `products.meta_title`, `meta_description` → đã có trong `2026_06_08_000001_create_store_tables.php`.
- File `2026_06_11_000001_add_seo_fields.php` chỉ để nâng cấp DB cũ; sẽ xóa khi squash.
