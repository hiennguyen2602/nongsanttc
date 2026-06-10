# Logic storefront (tóm tắt)

File này ghi các quy tắc nghiệp vụ / bảo mật phía khách. Code tương ứng nằm ở path bên dưới.

---

## 1. Giới hạn số request (rate-limit)

**Mục đích:** Chống bot spam đơn hàng, spam form liên hệ.

**Cách hoạt động:** Laravel đếm request theo **IP**. Vượt giới hạn → HTTP **429** (Too Many Requests), user thử lại sau ~1 phút.

**Cấu hình:** `src/routes/web.php` — middleware `throttle:X,1` (= tối đa **X lần / 1 phút / IP**).

| Route | Hành động | Giới hạn |
|-------|-----------|----------|
| `POST /lien-he` | Gửi form liên hệ | 5 / phút |
| `POST /dat-hang/ma-km` | Áp mã khuyến mãi | 15 / phút |
| `POST /dat-hang` | Xác nhận đặt hàng | 10 / phút |

**Không giới hạn (cố ý):** xem trang, thêm/sửa giỏ (`POST /gio-hang/them`, `PATCH /gio-hang`) — tránh ảnh hưởng khách mua bình thường.

**Muốn chỉnh:** sửa số trong `web.php` và cập nhật bảng trên.

---

## 2. Trang đặt hàng thành công

**Mục đích:** Không lộ ID đơn; không cho xem đơn người khác.

**URL:** `/dat-hang/thanh-cong/{token}` — `token` = 32 ký tự hex (cột `orders.public_token`).

**Quy tắc:**
- Token random khi tạo đơn (`OrderService`).
- Chỉ xem được **một lần** ngay sau khi đặt (session `checkout_success_token`).
- Đoán token hoặc mở tab ẩn danh → **403**.

**Code:** `CheckoutController`, model `Order`, migration `public_token`.

---

## 3. Giỏ hàng & checkout (giá / số lượng)

**Số lượng:** Không check tồn kho; validate max `unsigned int` (4_294_967_295) và tránh overflow `quantity × giá`.

**Giá lúc checkout:** Lấy lại từ DB (`CartService::resolveItems`), không tin giá cũ trong session.

**Tạo đơn:** Bọc `DB::transaction()` trong `OrderService::createFromCart`.

**Code:** `CartService`, `OrderService`, `CartController`, `CheckoutController`.

**Tối ưu:** `items()` gom 1 query slug cho giỏ cũ thiếu slug; `count()` đọc session (không query DB).

---

## 6. Dashboard admin

**Query:** Một `$query` lọc theo khoảng ngày — `statusCounts` (groupBy) + `totalOrders` (= tổng `statusCounts`) + `recentOrders` (clone, limit 8). Tránh gọi `count()` riêng.

**Code:** `DashboardController`.

---

## 4. Upload ảnh admin

**Giới hạn:** 20MB / file (`MAX_IMAGE_UPLOAD_MB` trong `.env`, `config/media.php`).

**Định dạng:** JPG, PNG, WebP, GIF — kiểm **hai lớp**:
- Controller: Laravel `image` + `mimes` (`image_upload_file_rules()`).
- Service: `getimagesize()` trên file thật; đuôi file lưu theo loại ảnh phát hiện được, không tin extension client.

**Ảnh giữ lại (hidden `existing_*`):** Không tin path client gửi — chỉ chấp nhận path đã thuộc bản ghi đang sửa, nằm trong `uploads/...`, không có `..`. Helper: `kept_upload_paths()`, `resolve_kept_upload_path()`.

**Code:** `ImageUploadService`, `helpers.php` (`image_upload_*`, `*_upload_path`), validation controller admin, ghi chú dưới input (`image_upload_hint()`).

---

## 5. Phân quyền admin — Quản trị user

**Hai cấp:**

| Role | Vào `/admin` | Menu **Quản trị** (user) |
|------|----------------|---------------------------|
| Admin (`type=1`) | Có | Có — thêm/sửa/xóa Admin & Staff |
| Staff (`type=2`) | Có | **Không** — 403 nếu gõ URL trực tiếp |
| Khách (`type=3`) | Không | Không |

**Quy tắc:**
- Route `admin/users/*` bọc middleware `administrator` (chỉ Admin).
- Sửa/xóa user: chỉ tài khoản `type` Admin hoặc Staff; user khách → 404.
- Menu sidebar: mục có `admin_only: true` trong `config/admin.php` chỉ hiện với Admin.

**Code:** `EnsureUserIsAdministrator`, `UserController::ensureManagedUser`, `routes/admin.php`, `config/admin.php`, `sidebar.blade.php`.

---

*Cập nhật logic mới → thêm mục vào file này + comment ngắn ở file code.*
