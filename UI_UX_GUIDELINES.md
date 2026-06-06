# 🎨 Hướng Dẫn Thiết Kế UI/UX (UI/UX Guidelines)

> Tài liệu quy định tiêu chuẩn thiết kế Giao diện Người dùng (UI) và Trải nghiệm Người dùng (UX) cho toàn bộ Hệ thống Quản lý Quán Billiards.
> **Tất cả lập trình viên và AI Agents phải tuân thủ nghiêm ngặt tài liệu này, không được tự ý sáng tạo khác với guideline.**

---

## 🎯 Mục Tiêu Cốt Lõi

Mọi giao diện trong hệ thống phải đảm bảo các tiêu chí:
1. **Đồng nhất:** Thống nhất tuyệt đối về màu sắc, khoảng cách (spacing), typography và các thành phần UI.
2. **Dễ sử dụng (Usability):** Trải nghiệm người dùng thân thiện, các thao tác rõ ràng.
3. **Responsive:** Hoạt động hoàn hảo trên mọi thiết bị (Desktop, Tablet, Mobile).
4. **Chuẩn Layout:** Tuân thủ cùng một cấu trúc bố cục xuyên suốt toàn hệ thống.

---

## 1. 📐 Cấu Trúc Layout Chuẩn (Trang Quản Trị)

Luôn sử dụng cấu trúc layout sau cho tất cả các trang quản trị:

```text
+---------------------------------------------------+
|                     HEADER                        |
+-----------+---------------------------------------+
|           |                                       |
| SIDEBAR   |            MAIN CONTENT               |
|           |                                       |
+-----------+---------------------------------------+
```

### Header
- **Thành phần:** Logo, Tên hệ thống, User dropdown, Notification.
- **Chiều cao cố định:** `64px`

### Sidebar
- **Chiều rộng cố định:** `280px`
- **Trạng thái:** Cố định (Fixed) bên trái.
- **Thành phần:** Dashboard, Quản lý dữ liệu, Báo cáo, Cài đặt.

### Main Content (Khu vực nội dung)
- **Padding:** `24px` (Không bao giờ đặt nội dung sát mép màn hình).

---

## 2. 🔤 Typography (Kiểu Chữ)

- **Font chính ưu tiên:** `Inter`
- **Font dự phòng (Fallback):** `sans-serif`

| Cấp độ | Kích thước | Độ đậm (Weight) | Ví dụ / Ghi chú |
| --- | --- | --- | --- |
| **Page Title** | `32px` | `700` (Bold) | Quản lý Người dùng |
| **Section Title** | `20px` | `600` (Semi-bold) | Thông tin chung |
| **Body Text** | `14px` | `400` (Regular) | Nội dung văn bản thường |
| **Table Text** | `14px` | `400` (Regular) | Dữ liệu trong bảng |

---

## 3. 🎨 Bảng Màu (Color Palette)

Chỉ sử dụng các mã màu sau, **không tự ý pha trộn màu mới**:

| Loại Màu | Mã Màu Hex | Mục đích sử dụng |
| --- | --- | --- |
| **Primary** | `#2563EB` | Nút bấm chính (Primary button), Link, Focus state. |
| **Success** | `#16A34A` | Thông báo thành công, Trạng thái hoàn thành. |
| **Warning** | `#F59E0B` | Cảnh báo, Trạng thái chờ. |
| **Danger** | `#DC2626` | Nút xóa, Lỗi, Thông báo thất bại. |
| **Background** | `#F8FAFC` | Màu nền tổng thể của toàn trang. |
| **Card** | `#FFFFFF` | Nền của các thẻ (Card nội dung). |

---

## 4. 🧩 Quy Tắc Thành Phần UI (UI Components)

### 4.1. Khối Nội Dung (Card)
Mọi khối nội dung **bắt buộc** phải nằm trong một Card.
- **Style:**
  ```css
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: small; /* Có thể dùng tailwind: shadow-sm */
  ```

### 4.2. Nút Bấm (Button)
- **Primary Button (Thao tác chính):** Nền Primary, chữ Trắng, chiều cao `40px`. (VD: *Tạo mới, Lưu*)
- **Secondary Button (Thao tác phụ):** Nền Trắng, viền Xám (`border: 1px solid gray;`). (VD: *Hủy, Quay lại*)
- **Danger Button (Thao tác nguy hiểm):** Màu đỏ (Danger). (VD: *Xóa*)

### 4.3. Biểu Mẫu (Form)
- **Cấu trúc:** Phải dùng flexbox với hướng dọc (`flex-direction: column`) và khoảng cách (`gap: 16px`).
- **Nhãn (Label):** Luôn nằm **trên** ô nhập liệu (Input). Không đặt label nằm ngang với input.
- **Input Height:** `40px`
- **Xác thực (Validation):** Lỗi phải hiển thị ngay bên dưới input tương ứng với chữ màu đỏ (Danger).

### 4.4. Bảng Dữ Liệu (Table)
Mỗi bảng dữ liệu phải luôn có đầy đủ 3 thành phần:
1. **Search** (Thanh tìm kiếm)
2. **Filter** (Bộ lọc)
3. **Pagination** (Phân trang)
- **Cột Action (Hành động):** Luôn giữ thứ tự chuẩn: **View** ➔ **Edit** ➔ **Delete**. Không thay đổi vị trí.

### 4.5. Hộp Thoại (Modal)
- **Kích thước tối đa:** `max-width: 600px`
- **Cấu trúc bắt buộc:**
  1. Title (Tiêu đề)
  2. Content (Nội dung)
  3. Footer chứa các nút: `[Cancel]` `[Save]`

---

## 5. 📱 Responsive & Layout Thích Ứng

Sử dụng Breakpoint ở mức `768px` (Mobile/Tablet):

| Thành phần | Desktop (>= 768px) | Mobile (< 768px) |
| --- | --- | --- |
| **Sidebar** | Fixed Sidebar (Ghim cố định) | Drawer (Menu ẩn/vuốt ra) |
| **Table** | Bảng truyền thống (Table) | Danh sách thẻ (Card List) |

---

## 6. 🚦 Các Trạng Thái Đặc Biệt

### Trạng thái trống (Empty State)
Tuyệt đối **không để màn hình trắng** khi không có dữ liệu. Bắt buộc hiển thị:
- Biểu tượng/Hình ảnh minh họa (VD: 📄)
- Dòng chữ: *"Không có dữ liệu"*
- Nút hành động: `[Tạo mới]` (nếu có quyền).

### Trạng thái tải (Loading State)
Khi chờ dữ liệu, tuyệt đối **không để màn hình trắng**. Bắt buộc hiển thị:
- **Skeleton Loader** (Ưu tiên)
- Hoặc **Spinner** quay vòng.

---

## 7. 🛠️ Quy Tắc Laravel Blade

- **Kế thừa Layout:** Kế thừa file layout chính: `@extends('layouts.app')`
- **Sử dụng Component:** Tái sử dụng tối đa mã HTML, **không lặp lại HTML** giữa các màn hình. Các component bắt buộc dùng chung:
  - `components/button.blade.php`
  - `components/input.blade.php`
  - `components/modal.blade.php`
  - `components/table.blade.php`
  - `components/card.blade.php`

---

## 8. 🤖 Yêu Cầu Đối Với AI Agent

Khi nhận yêu cầu tạo mới hoặc chỉnh sửa giao diện, AI Agent **phải tuân thủ**:
1. Luôn tái sử dụng các components đã có sẵn.
2. Không tự ý tạo mã màu mới, dùng đúng bảng màu ở mục 3.
3. Không tự ý thêm font chữ mới.
4. Không thay đổi layout tổng thể của hệ thống.
5. Code bắt buộc phải hỗ trợ responsive.
6. Form tạo/sửa luôn phải đi kèm validation state (trạng thái lỗi).
7. Table luôn phải bao gồm thanh search và phân trang (pagination).
8. Tuân thủ Grid System với **spacing hệ số 8** (8px, 16px, 24px, 32px...).
9. **Phong cách thiết kế:** Mọi màn hình mới phải mang phong cách y hệt các màn hình hiện có.
10. **Tự nghiên cứu:** Nếu chưa rõ UI cần theo mẫu nào, AI hãy tự động tìm một file màn hình tương tự trong thư mục `resources/views` để xem mẫu trước khi sinh code.
