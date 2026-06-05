# AGENTS.md — Hệ Thống Quản Lý Quán Billiards

> Tài liệu quy định phát triển dự án dành cho toàn bộ thành viên nhóm.  
> **Đọc kỹ và tuân thủ nghiêm ngặt trước khi bắt đầu code.**

---

## Mục Lục

1. [Thông Tin Dự Án](#1-thông-tin-dự-án)
2. [Phân Công Thành Viên](#2-phân-công-thành-viên)
3. [Kiến Trúc Hệ Thống](#3-kiến-trúc-hệ-thống)
4. [Cấu Trúc Thư Mục](#4-cấu-trúc-thư-mục)
5. [Database Schema](#5-database-schema)
6. [Danh Sách Module & Chức Năng](#6-danh-sách-module--chức-năng)
7. [Quy Tắc Code](#7-quy-tắc-code)
8. [Quy Tắc Đặt Tên](#8-quy-tắc-đặt-tên)
9. [Git Workflow](#9-git-workflow)
10. [Kiểm Tra Trước Khi Commit](#10-kiểm-tra-trước-khi-commit)
11. [Công Cụ & Môi Trường](#11-công-cụ--môi-trường)

---

## 1. Thông Tin Dự Án

| Mục | Chi tiết |
|---|---|
| **Tên dự án** | Hệ thống quản lý quán Billiards |
| **Laravel** | 12.x |
| **PHP** | 8.3+ |
| **Database** | MySQL 8+ |
| **Frontend** | Blade Template + Bootstrap 5 + JavaScript |
| **Version Control** | Git & GitHub |
| **NodeJS** | 18+ |

### Mục Tiêu

Xây dựng hệ thống web hỗ trợ quản lý toàn bộ hoạt động quán Billiards:

- Quản lý tài khoản và phân quyền người dùng
- Quản lý bàn chơi và trạng thái bàn
- Quản lý đặt bàn trước
- Quản lý phiên chơi và tính tiền tự động
- Quản lý sản phẩm (đồ ăn, đồ uống)
- Lập hóa đơn và thanh toán
- Dashboard thống kê doanh thu trực quan

---

## 2. Phân Công Thành Viên

### Thành Viên 1 — Nguyễn Quốc Doanh

**Branch:** `dev-doanh`

| Module | Chức năng |
|---|---|
| **Auth** | Đăng nhập, Đăng xuất, Đổi mật khẩu |
| **User** | CRUD tài khoản, Phân quyền, Khóa/Mở tài khoản |
| **Dashboard** | Thống kê doanh thu, Biểu đồ, Báo cáo tổng quan |

**File phụ trách:**

```
app/Http/Controllers/AuthController.php
app/Http/Controllers/UserController.php
app/Http/Controllers/DashboardController.php
app/Http/Requests/Auth/LoginRequest.php
app/Http/Requests/Auth/ChangePasswordRequest.php
app/Http/Requests/User/StoreUserRequest.php
app/Http/Requests/User/UpdateUserRequest.php
app/Services/Auth/AuthService.php
app/Services/User/UserService.php
app/Services/Dashboard/DashboardService.php
resources/views/auth/
resources/views/users/
resources/views/dashboard/
```

**Migration phụ trách:**

```
create_roles_table
create_users_table (có role_id, name, email, phone, avatar, status)
```

---

### Thành Viên 2 — Nguyễn Thái Dương

**Branch:** `dev-duong`

| Module | Chức năng |
|---|---|
| **Table** | CRUD bàn chơi, Quản lý trạng thái bàn |
| **Booking** | Tạo/Hủy/Xác nhận đặt bàn, Lịch sử đặt bàn |

**File phụ trách:**

```
app/Http/Controllers/TableController.php
app/Http/Controllers/BookingController.php
app/Http/Requests/Table/StoreTableRequest.php
app/Http/Requests/Table/UpdateTableRequest.php
app/Http/Requests/Booking/StoreBookingRequest.php
app/Http/Requests/Booking/UpdateBookingRequest.php
app/Services/Table/TableService.php
app/Services/Booking/BookingService.php
resources/views/tables/
resources/views/bookings/
```

**Migration phụ trách:**

```
create_billiard_tables_table (table_number, table_type, price_per_hour, status, description)
create_bookings_table (user_id, billiard_table_id, booking_date, start_time, end_time, status, note)
```

---

### Thành Viên 3 — Lê Hữu Hiệu

**Branch:** `dev-hieu`

| Module | Chức năng |
|---|---|
| **Session** | Bắt đầu/Kết thúc phiên chơi, Tính giờ, Tính tiền bàn |
| **Invoice** | Tạo hóa đơn, Thanh toán, Lịch sử hóa đơn |

**File phụ trách:**

```
app/Http/Controllers/TableSessionController.php
app/Http/Controllers/InvoiceController.php
app/Http/Requests/Session/StartSessionRequest.php
app/Http/Requests/Session/EndSessionRequest.php
app/Http/Requests/Invoice/StoreInvoiceRequest.php
app/Services/Session/TableSessionService.php
app/Services/Invoice/InvoiceService.php
resources/views/sessions/
resources/views/invoices/
```

**Migration phụ trách:**

```
create_table_sessions_table (billiard_table_id, customer_id, start_time, end_time, total_hours, table_price, status)
create_invoices_table (table_session_id, staff_id, subtotal, discount, total_amount, payment_method, payment_status)
create_invoice_details_table (invoice_id, product_id, quantity, unit_price, total_price)
```

---

### Thành Viên 4 — Thân Đức Minh

**Branch:** `dev-minh`

| Module | Chức năng |
|---|---|
| **Product** | CRUD sản phẩm, Quản lý tồn kho |
| **Category** | CRUD danh mục sản phẩm |

**File phụ trách:**

```
app/Http/Controllers/ProductController.php
app/Http/Controllers/CategoryController.php
app/Http/Requests/Product/StoreProductRequest.php
app/Http/Requests/Product/UpdateProductRequest.php
app/Http/Requests/Category/StoreCategoryRequest.php
app/Http/Requests/Category/UpdateCategoryRequest.php
app/Services/Product/ProductService.php
resources/views/products/
resources/views/categories/
```

**Migration phụ trách:**

```
create_categories_table (name, description)
create_products_table (category_id, name, price, quantity, image, description, status)
```

---

## 3. Kiến Trúc Hệ Thống

```
HTTP Request
     │
     ▼
 Middleware  (Auth, Role check)
     │
     ▼
 Form Request  (Validation)
     │
     ▼
 Controller  (Nhận request → gọi Service → trả response)
     │
     ▼
  Service  (Business Logic, Tính toán, Kiểm tra nghiệp vụ)
     │
     ▼
   Model  (Eloquent ORM — Relationship, Scope, Cast)
     │
     ▼
 Database  (MySQL)
```

### Nguyên Tắc Kiến Trúc

| Layer | Trách nhiệm | Không được |
|---|---|---|
| **Controller** | Nhận request, gọi Service, trả response | Viết business logic, query DB |
| **Service** | Business logic, tính toán, kiểm tra nghiệp vụ | Gọi trực tiếp DB ngoài Model |
| **Model** | Relationship, Scope, Accessor, Mutator, Cast | Viết business logic lớn |
| **Form Request** | Validate dữ liệu đầu vào | — |
| **Middleware** | Kiểm tra auth, phân quyền | — |

---

## 4. Cấu Trúc Thư Mục

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── DashboardController.php
│   │   ├── TableController.php
│   │   ├── BookingController.php
│   │   ├── TableSessionController.php
│   │   ├── InvoiceController.php
│   │   ├── ProductController.php
│   │   └── CategoryController.php
│   │
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── LoginRequest.php
│   │   │   └── ChangePasswordRequest.php
│   │   ├── User/
│   │   │   ├── StoreUserRequest.php
│   │   │   └── UpdateUserRequest.php
│   │   ├── Table/
│   │   │   ├── StoreTableRequest.php
│   │   │   └── UpdateTableRequest.php
│   │   ├── Booking/
│   │   │   ├── StoreBookingRequest.php
│   │   │   └── UpdateBookingRequest.php
│   │   ├── Session/
│   │   │   ├── StartSessionRequest.php
│   │   │   └── EndSessionRequest.php
│   │   ├── Invoice/
│   │   │   └── StoreInvoiceRequest.php
│   │   ├── Product/
│   │   │   ├── StoreProductRequest.php
│   │   │   └── UpdateProductRequest.php
│   │   └── Category/
│   │       ├── StoreCategoryRequest.php
│   │       └── UpdateCategoryRequest.php
│   │
│   └── Middleware/
│       └── CheckRole.php
│
├── Models/
│   ├── Role.php
│   ├── User.php
│   ├── BilliardTable.php
│   ├── Booking.php
│   ├── TableSession.php
│   ├── Category.php
│   ├── Product.php
│   ├── Invoice.php
│   └── InvoiceDetail.php
│
├── Services/
│   ├── Auth/
│   │   └── AuthService.php
│   ├── User/
│   │   └── UserService.php
│   ├── Dashboard/
│   │   └── DashboardService.php
│   ├── Table/
│   │   └── TableService.php
│   ├── Booking/
│   │   └── BookingService.php
│   ├── Session/
│   │   └── TableSessionService.php
│   ├── Invoice/
│   │   └── InvoiceService.php
│   └── Product/
│       └── ProductService.php
│
└── Providers/
    └── AppServiceProvider.php

resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── change-password.blade.php
    ├── dashboard/
    │   └── index.blade.php
    ├── users/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── tables/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── bookings/
    │   ├── index.blade.php
    │   └── create.blade.php
    ├── sessions/
    │   └── index.blade.php
    ├── invoices/
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── products/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── categories/
        ├── index.blade.php
        └── create.blade.php

database/
├── migrations/
│   ├── create_roles_table.php
│   ├── create_users_table.php
│   ├── create_billiard_tables_table.php
│   ├── create_bookings_table.php
│   ├── create_table_sessions_table.php
│   ├── create_categories_table.php
│   ├── create_products_table.php
│   ├── create_invoices_table.php
│   └── create_invoice_details_table.php
├── seeders/
└── factories/
```

---

## 5. Database Schema

### Quan Hệ Tổng Thể (ERD)

```
roles ──────< users >──────< bookings >────── billiard_tables
                 │                                    │
                 │                                    │
                 └──────< table_sessions >────────────┘
                                  │
                                  ▼
                              invoices
                                  │
                                  ▼
                          invoice_details
                                  │
                                  ▼
                 categories ──< products
```

---

### Bảng: `roles`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | Auto increment |
| `name` | varchar UNIQUE | Tên vai trò: `admin`, `staff`, `customer` |
| `description` | varchar NULL | Mô tả vai trò |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `users`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `role_id` | bigint FK | → `roles.id` |
| `name` | varchar | Họ tên |
| `email` | varchar UNIQUE | Email đăng nhập |
| `phone` | varchar NULL | Số điện thoại |
| `avatar` | varchar NULL | Đường dẫn ảnh đại diện |
| `password` | varchar | Bcrypt hash |
| `status` | boolean | `true` = active, `false` = locked |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `billiard_tables`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `table_number` | varchar UNIQUE | Số hiệu bàn, ví dụ: `B01` |
| `table_type` | enum | `POOL`, `SNOOKER`, `CAROM` |
| `price_per_hour` | decimal(10,2) | Giá thuê mỗi giờ |
| `status` | enum | `AVAILABLE`, `RESERVED`, `PLAYING`, `MAINTENANCE` |
| `description` | text NULL | Mô tả bàn |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `bookings`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `user_id` | bigint FK | → `users.id` (CASCADE DELETE) |
| `billiard_table_id` | bigint FK | → `billiard_tables.id` (CASCADE DELETE) |
| `booking_date` | date | Ngày đặt bàn |
| `start_time` | datetime | Giờ bắt đầu dự kiến |
| `end_time` | datetime | Giờ kết thúc dự kiến |
| `status` | enum | `PENDING`, `CONFIRMED`, `CANCELLED`, `COMPLETED` |
| `note` | text NULL | Ghi chú |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `table_sessions`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `billiard_table_id` | bigint FK | → `billiard_tables.id` (CASCADE DELETE) |
| `customer_id` | bigint FK NULL | → `users.id` (SET NULL) |
| `start_time` | datetime | Thời điểm bắt đầu phiên |
| `end_time` | datetime NULL | Thời điểm kết thúc phiên |
| `total_hours` | decimal(5,2) | Tổng số giờ chơi |
| `table_price` | decimal(10,2) | Tiền thuê bàn |
| `status` | enum | `PLAYING`, `FINISHED` |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `categories`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `name` | varchar | Tên danh mục |
| `description` | text NULL | Mô tả |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `products`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `category_id` | bigint FK | → `categories.id` (CASCADE DELETE) |
| `name` | varchar | Tên sản phẩm |
| `price` | decimal(10,2) | Giá bán |
| `quantity` | int | Số lượng tồn kho, mặc định 0 |
| `image` | varchar NULL | Đường dẫn ảnh |
| `description` | text NULL | Mô tả |
| `status` | boolean | `true` = đang bán, `false` = ngừng bán |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `invoices`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `table_session_id` | bigint FK | → `table_sessions.id` (CASCADE DELETE) |
| `staff_id` | bigint FK NULL | → `users.id` (SET NULL) — nhân viên lập hóa đơn |
| `subtotal` | decimal(12,2) | Tổng tiền trước giảm giá |
| `discount` | decimal(12,2) | Tiền giảm giá, mặc định 0 |
| `total_amount` | decimal(12,2) | Thành tiền sau giảm giá |
| `payment_method` | enum | `CASH`, `BANKING` |
| `payment_status` | enum | `UNPAID`, `PAID` |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

### Bảng: `invoice_details`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | — |
| `invoice_id` | bigint FK | → `invoices.id` (CASCADE DELETE) |
| `product_id` | bigint FK | → `products.id` (CASCADE DELETE) |
| `quantity` | int | Số lượng |
| `unit_price` | decimal(10,2) | Giá đơn vị tại thời điểm mua |
| `total_price` | decimal(12,2) | Thành tiền = quantity × unit_price |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

---

## 6. Danh Sách Module & Chức Năng

### Module Auth — Nguyễn Quốc Doanh

**Đường dẫn:** `AuthController` → `AuthService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Hiển thị trang login | `GET` | `/login` | Redirect về dashboard nếu đã login |
| Đăng nhập | `POST` | `/login` | Xác thực email + password |
| Đăng xuất | `POST` | `/logout` | Hủy session, redirect về login |
| Hiển thị đổi mật khẩu | `GET` | `/change-password` | Form đổi mật khẩu |
| Đổi mật khẩu | `POST` | `/change-password` | Kiểm tra mật khẩu cũ, cập nhật mới |

**Validate LoginRequest:**

```php
'email'    => 'required|email',
'password' => 'required|min:6',
```

**Validate ChangePasswordRequest:**

```php
'current_password' => 'required',
'new_password'     => 'required|min:8|confirmed',
```

---

### Module User — Nguyễn Quốc Doanh

**Đường dẫn:** `UserController` → `UserService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách user | `GET` | `/users` | Phân trang, tìm kiếm, lọc theo role |
| Tạo user mới | `GET/POST` | `/users/create` | Form + xử lý tạo tài khoản |
| Xem chi tiết | `GET` | `/users/{id}` | Thông tin user |
| Sửa user | `GET/PUT` | `/users/{id}/edit` | Cập nhật thông tin |
| Xóa user | `DELETE` | `/users/{id}` | Xóa mềm hoặc cứng |
| Khóa/Mở tài khoản | `PATCH` | `/users/{id}/toggle-status` | Đổi status true/false |

**Validate StoreUserRequest:**

```php
'role_id'  => 'required|exists:roles,id',
'name'     => 'required|string|max:255',
'email'    => 'required|email|unique:users,email',
'phone'    => 'nullable|string|max:15',
'password' => 'required|min:8|confirmed',
'status'   => 'boolean',
```

---

### Module Dashboard — Nguyễn Quốc Doanh

**Đường dẫn:** `DashboardController` → `DashboardService`

| Thống kê | Mô tả |
|---|---|
| Doanh thu hôm nay / tháng / năm | Tổng `total_amount` từ `invoices` theo khoảng thời gian |
| Số lượng đặt bàn | Đếm `bookings` theo trạng thái |
| Bàn đang hoạt động | Đếm `billiard_tables` status = `PLAYING` |
| Sản phẩm bán chạy | Top sản phẩm theo tổng `quantity` trong `invoice_details` |
| Bàn được dùng nhiều nhất | Đếm số phiên theo `billiard_table_id` |

---

### Module Table — Nguyễn Thái Dương

**Đường dẫn:** `TableController` → `TableService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách bàn | `GET` | `/tables` | Hiển thị tất cả bàn và trạng thái |
| Tạo bàn | `GET/POST` | `/tables/create` | Thêm bàn mới |
| Sửa bàn | `GET/PUT` | `/tables/{id}/edit` | Cập nhật thông tin bàn |
| Xóa bàn | `DELETE` | `/tables/{id}` | Chỉ xóa khi bàn AVAILABLE |
| Đổi trạng thái | `PATCH` | `/tables/{id}/status` | Thay đổi status bàn |

**Trạng thái bàn (BilliardTable Enum):**

```
AVAILABLE   → Bàn trống, có thể đặt hoặc chơi ngay
RESERVED    → Đã được đặt trước (có booking CONFIRMED)
PLAYING     → Đang có người chơi (có table_session PLAYING)
MAINTENANCE → Đang bảo trì, không thể sử dụng
```

**Validate StoreTableRequest:**

```php
'table_number'   => 'required|string|unique:billiard_tables,table_number',
'table_type'     => 'required|in:POOL,SNOOKER,CAROM',
'price_per_hour' => 'required|numeric|min:0',
'status'         => 'required|in:AVAILABLE,RESERVED,PLAYING,MAINTENANCE',
'description'    => 'nullable|string',
```

---

### Module Booking — Nguyễn Thái Dương

**Đường dẫn:** `BookingController` → `BookingService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách booking | `GET` | `/bookings` | Danh sách đặt bàn, lọc theo trạng thái |
| Tạo booking | `GET/POST` | `/bookings/create` | Chọn bàn, thời gian đặt |
| Xác nhận booking | `PATCH` | `/bookings/{id}/confirm` | Đổi status → CONFIRMED, bàn → RESERVED |
| Hủy booking | `PATCH` | `/bookings/{id}/cancel` | Đổi status → CANCELLED, bàn → AVAILABLE |
| Hoàn tất booking | `PATCH` | `/bookings/{id}/complete` | Đổi status → COMPLETED |

**Luồng xử lý booking:**

```
PENDING → CONFIRMED → COMPLETED
   └──────────────→ CANCELLED
```

**Validate StoreBookingRequest:**

```php
'user_id'           => 'required|exists:users,id',
'billiard_table_id' => 'required|exists:billiard_tables,id',
'booking_date'      => 'required|date|after_or_equal:today',
'start_time'        => 'required|date_format:Y-m-d H:i:s',
'end_time'          => 'required|date_format:Y-m-d H:i:s|after:start_time',
'note'              => 'nullable|string',
```

---

### Module Session — Lê Hữu Hiệu

**Đường dẫn:** `TableSessionController` → `TableSessionService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách phiên | `GET` | `/sessions` | Xem phiên đang chơi và lịch sử |
| Bắt đầu phiên | `POST` | `/sessions/start` | Tạo phiên mới, bàn → PLAYING |
| Kết thúc phiên | `PATCH` | `/sessions/{id}/end` | Tính giờ, tiền, bàn → AVAILABLE |
| Chi tiết phiên | `GET` | `/sessions/{id}` | Xem thông tin phiên chơi |

**Logic tính tiền phiên:**

```
total_hours = (end_time - start_time) / 3600  (làm tròn 2 chữ số thập phân)
table_price = total_hours × price_per_hour
```

**Validate StartSessionRequest:**

```php
'billiard_table_id' => 'required|exists:billiard_tables,id',
'customer_id'       => 'nullable|exists:users,id',
'start_time'        => 'required|date_format:Y-m-d H:i:s',
```

---

### Module Invoice — Lê Hữu Hiệu

**Đường dẫn:** `InvoiceController` → `InvoiceService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách hóa đơn | `GET` | `/invoices` | Lịch sử hóa đơn, lọc theo trạng thái |
| Tạo hóa đơn | `POST` | `/invoices` | Tạo từ phiên chơi + thêm sản phẩm |
| Chi tiết hóa đơn | `GET` | `/invoices/{id}` | Xem chi tiết + in hóa đơn |
| Thanh toán | `PATCH` | `/invoices/{id}/pay` | Đổi payment_status → PAID |

**Logic tính hóa đơn:**

```
subtotal     = table_price + sum(invoice_details.total_price)
total_amount = subtotal - discount
```

**Validate StoreInvoiceRequest:**

```php
'table_session_id'     => 'required|exists:table_sessions,id',
'staff_id'             => 'nullable|exists:users,id',
'discount'             => 'nullable|numeric|min:0',
'payment_method'       => 'required|in:CASH,BANKING',
'products'             => 'nullable|array',
'products.*.product_id'=> 'required|exists:products,id',
'products.*.quantity'  => 'required|integer|min:1',
```

---

### Module Product — Thân Đức Minh

**Đường dẫn:** `ProductController` → `ProductService`

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách sản phẩm | `GET` | `/products` | Phân trang, tìm kiếm, lọc theo danh mục |
| Tạo sản phẩm | `GET/POST` | `/products/create` | Thêm sản phẩm mới |
| Sửa sản phẩm | `GET/PUT` | `/products/{id}/edit` | Cập nhật thông tin |
| Xóa sản phẩm | `DELETE` | `/products/{id}` | Chỉ xóa khi không có trong hóa đơn |
| Đổi trạng thái | `PATCH` | `/products/{id}/toggle` | Bật/tắt bán sản phẩm |

**Validate StoreProductRequest:**

```php
'category_id' => 'required|exists:categories,id',
'name'        => 'required|string|max:255',
'price'       => 'required|numeric|min:0',
'quantity'    => 'required|integer|min:0',
'image'       => 'nullable|image|max:2048',
'description' => 'nullable|string',
'status'      => 'boolean',
```

---

### Module Category — Thân Đức Minh

**Đường dẫn:** `CategoryController` (Service nội bộ hoặc tích hợp ProductService)

| Chức năng | Method | Route | Mô tả |
|---|---|---|---|
| Danh sách danh mục | `GET` | `/categories` | Xem tất cả danh mục |
| Tạo danh mục | `GET/POST` | `/categories/create` | Thêm danh mục mới |
| Sửa danh mục | `GET/PUT` | `/categories/{id}/edit` | Cập nhật |
| Xóa danh mục | `DELETE` | `/categories/{id}` | Chỉ xóa khi không còn sản phẩm |

---

## 7. Quy Tắc Code

### 7.1 Coding Standards

- Tuân thủ **PSR-12** (dùng formatter / Laravel Pint).
- Mỗi class chỉ thực hiện **một nhiệm vụ** (Single Responsibility Principle).
- Sử dụng **Type Hint** đầy đủ cho tham số và kiểu trả về.
- Ưu tiên **Dependency Injection** thay vì `new Class()` trực tiếp.
- Không dùng biến tên một ký tự (ngoại trừ vòng lặp).
- Không để lại `dd()`, `dump()`, `var_dump()` trong code.

### 7.2 Quy Tắc Controller

Controller chỉ được làm:

```php
public function store(StoreBookingRequest $request): RedirectResponse
{
    // 1. Nhận dữ liệu đã validate từ Form Request
    $data = $request->validated();

    // 2. Gọi Service xử lý
    $this->bookingService->createBooking($data);

    // 3. Trả về response
    return redirect()->route('bookings.index')
        ->with('success', 'Đặt bàn thành công.');
}
```

Controller **không được**:

```php
// ❌ SAI — Viết query trong controller
$bookings = Booking::where('status', 'PENDING')->get();

// ❌ SAI — Viết logic trong controller
if ($table->status !== 'AVAILABLE') {
    throw new Exception('Bàn không trống');
}
```

### 7.3 Quy Tắc Service

Service chứa toàn bộ business logic:

```php
class BookingService
{
    public function __construct(
        private readonly Booking $bookingModel,
        private readonly BilliardTable $tableModel,
    ) {}

    public function createBooking(array $data): Booking
    {
        // Kiểm tra bàn còn trống không
        $table = $this->tableModel->findOrFail($data['billiard_table_id']);

        if ($table->status !== 'AVAILABLE') {
            throw new \Exception('Bàn hiện không khả dụng.');
        }

        // Tạo booking
        return $this->bookingModel->create($data);
    }
}
```

### 7.4 Quy Tắc Model

Model chỉ chứa relationship, scope, cast, accessor/mutator:

```php
class BilliardTable extends Model
{
    protected $fillable = ['table_number', 'table_type', 'price_per_hour', 'status', 'description'];

    // Relationship
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Scope
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'AVAILABLE');
    }
}
```

### 7.5 Quy Tắc Database

- Mỗi thành viên **chỉ sửa migration thuộc module của mình**.
- **Không sửa migration đã merge vào `develop`**.
- Tất cả khóa ngoại phải dùng `foreignId()`.
- Luôn khai báo relationship trong Model.
- Sử dụng Migration — không chỉnh sửa trực tiếp Database.
- Không viết raw SQL khi Eloquent đã hỗ trợ.

---

## 8. Quy Tắc Đặt Tên

### Controller

```
[Tên Module]Controller
```

| Đúng | Sai |
|---|---|
| `UserController` | `UsersController`, `user_controller` |
| `BookingController` | `BookController`, `BookingCtrl` |
| `TableSessionController` | `SessionController` (không rõ nghĩa) |

### Service

```
[Tên Module]Service
```

| Đúng | Sai |
|---|---|
| `AuthService` | `AuthenticationService` |
| `TableSessionService` | `SessionSvc` |
| `InvoiceService` | `BillService` |

### Form Request

```
[Store|Update|Delete][Module]Request
```

| Đúng | Sai |
|---|---|
| `StoreBookingRequest` | `BookingRequest`, `CreateBooking` |
| `UpdateUserRequest` | `EditUserRequest` |
| `StoreInvoiceRequest` | `InvoiceRequest` |

### Model

Tên đơn, PascalCase, khớp với tên bảng (số nhiều → số ít):

| Model | Bảng |
|---|---|
| `User` | `users` |
| `BilliardTable` | `billiard_tables` |
| `TableSession` | `table_sessions` |
| `InvoiceDetail` | `invoice_details` |

### Method

Dùng camelCase, động từ + danh từ rõ nghĩa:

```php
// ✅ Đúng
public function createBooking(array $data): Booking {}
public function calculateSessionPrice(TableSession $session): float {}
public function toggleUserStatus(User $user): void {}

// ❌ Sai
public function booking(array $data) {}
public function calc() {}
public function do(User $user) {}
```

### Route

Dùng `kebab-case` cho URI, `snake_case` cho tên route:

```php
// ✅ Đúng
Route::get('/billiard-tables', [TableController::class, 'index'])->name('tables.index');
Route::patch('/bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');

// ❌ Sai
Route::get('/billiardTables', ...);
Route::get('/Bookings/Confirm', ...);
```

### View

Dùng `kebab-case`, đặt trong thư mục theo module:

```
resources/views/
├── tables/index.blade.php
├── tables/create.blade.php
├── bookings/index.blade.php
└── auth/login.blade.php
```

### Variable

Dùng camelCase, tên đủ nghĩa:

```php
// ✅ Đúng
$billiardTable = BilliardTable::find($id);
$activeBookings = $this->bookingService->getActiveBookings();

// ❌ Sai
$t = BilliardTable::find($id);
$data = $this->bookingService->getActiveBookings();
```

---

## 9. Git Workflow

### Cây Branch

```
main (production)
 └── develop (integration)
      ├── dev-doanh  (Nguyễn Quốc Doanh  — Auth, User, Dashboard)
      ├── dev-duong  (Nguyễn Thái Dương  — Table, Booking)
      ├── dev-hieu   (Lê Hữu Hiệu        — Session, Invoice)
      └── dev-minh   (Thân Đức Minh      — Product, Category)
```

### Quy Trình Làm Việc

```bash
# 1. Luôn cập nhật develop trước khi bắt đầu
git checkout develop
git pull origin develop

# 2. Checkout về branch cá nhân
git checkout dev-doanh

# 3. Merge develop vào branch cá nhân để cập nhật
git merge develop

# 4. Code và commit thường xuyên
git add .
git commit -m "[feat] Thêm chức năng đăng nhập"

# 5. Push lên remote
git push origin dev-doanh

# 6. Tạo Pull Request vào develop trên GitHub
# Tiêu đề: [Module] Mô tả chức năng
# Ví dụ: [Auth] Hoàn thành chức năng đăng nhập và đổi mật khẩu
```

### Quy Tắc Branch

| Quy tắc | Lý do |
|---|---|
| ❌ Không push trực tiếp lên `main` | Bảo vệ production |
| ❌ Không push trực tiếp lên `develop` | Tránh conflict |
| ✅ Chỉ làm việc trên branch cá nhân | Tách biệt công việc |
| ✅ Tạo PR vào `develop` khi xong | Trưởng nhóm review |
| ✅ Resolve conflict trước khi merge | Tránh lỗi |

---

### Quy Tắc Commit Message

Format chuẩn:

```
[type] Mô tả ngắn gọn, rõ ràng
```

| Type | Khi nào dùng |
|---|---|
| `[feat]` | Thêm tính năng mới |
| `[fix]` | Sửa lỗi |
| `[refactor]` | Tái cấu trúc code, không thay đổi chức năng |
| `[style]` | Chỉnh sửa format, căn lề, đặt tên |
| `[docs]` | Cập nhật tài liệu |
| `[test]` | Thêm hoặc sửa test |
| `[chore]` | Cập nhật package, config |
| `[db]` | Thêm/sửa migration, seeder |

**Ví dụ commit đúng:**

```bash
git commit -m "[feat] Thêm chức năng tạo đặt bàn"
git commit -m "[fix] Sửa lỗi tính sai tiền phiên chơi khi dưới 1 giờ"
git commit -m "[db] Thêm migration bảng invoice_details"
git commit -m "[refactor] Tách logic tính hóa đơn sang InvoiceService"
git commit -m "[docs] Cập nhật AGENTS.md phần phân công thành viên"
```

**Ví dụ commit sai:**

```bash
git commit -m "fix bug"           # ❌ Không rõ fix gì
git commit -m "update"            # ❌ Không có thông tin
git commit -m "WIP"               # ❌ Không commit code dở
```

---

### Quy Tắc Pull Request

Tiêu đề PR:

```
[Module] Mô tả chức năng đã hoàn thành
```

Nội dung PR phải bao gồm:

```markdown
## Chức năng đã hoàn thành
- [ ] Tạo đặt bàn
- [ ] Xác nhận đặt bàn
- [ ] Hủy đặt bàn

## File đã thay đổi
- app/Http/Controllers/BookingController.php
- app/Services/Booking/BookingService.php
- app/Http/Requests/Booking/StoreBookingRequest.php
- resources/views/bookings/

## Database thay đổi
- Không có

## Ghi chú
- Đã test thủ công các trường hợp đặt bàn bình thường
- Chưa xử lý trường hợp đặt bàn trùng giờ (sẽ làm ở task sau)
```

---

## 10. Kiểm Tra Trước Khi Commit

### Checklist Bắt Buộc

```
[ ] Không còn lỗi cú pháp PHP (chạy php -l file.php)
[ ] Không còn dd(), dump(), var_dump() trong code
[ ] Không commit file .env
[ ] Đã chạy migrate thành công (php artisan migrate)
[ ] Đã test chức năng vừa thực hiện thủ công
[ ] Code tuân thủ PSR-12
[ ] Không còn code comment thừa (// TODO, // test, v.v.)
[ ] Tên biến, hàm, class đúng quy tắc đặt tên
```

### Lệnh Kiểm Tra Trước Khi Push

```bash
# Kiểm tra cú pháp PHP
php artisan route:list          # Xem route có đăng ký đúng không
php artisan config:clear        # Clear cache config
php artisan view:clear          # Clear cache view
php artisan migrate --pretend   # Xem migration sẽ chạy gì

# Kiểm tra code style (nếu có Laravel Pint)
./vendor/bin/pint --test
```

---

## 11. Công Cụ & Môi Trường

### Phiên Bản

| Công cụ | Phiên bản |
|---|---|
| Laravel | 12.x |
| PHP | 8.3+ |
| MySQL | 8+ |
| NodeJS | 18+ |
| Composer | 2.x |

### VS Code Extensions (Khuyến nghị)

| Extension | Mục đích |
|---|---|
| PHP Intelephense | Autocomplete PHP |
| Laravel Blade Snippets | Hỗ trợ Blade template |
| GitLens | Xem lịch sử Git |
| Thunder Client | Test API thay Postman |
| Prettier | Format code |
| Laravel Artisan | Chạy Artisan từ VS Code |
| DotENV | Highlight file .env |

### Lệnh Hay Dùng

```bash
# Migrate
php artisan migrate
php artisan migrate:fresh --seed   # Reset DB + seed dữ liệu

# Tạo file
php artisan make:controller BookingController
php artisan make:model Booking -m              # Model + Migration
php artisan make:request StoreBookingRequest
php artisan make:service BookingService        # (nếu có package)

# Clear cache
php artisan optimize:clear

# Chạy server local
php artisan serve
npm run dev                                    # Vite (nếu có)
```

### Thiết Lập Môi Trường

```bash
# Clone dự án
git clone https://github.com/your-org/billiards-management.git
cd billiards-management

# Cài dependencies
composer install
npm install

# Tạo file .env
cp .env.example .env
php artisan key:generate

# Cấu hình database trong .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billiards_management
DB_USERNAME=root
DB_PASSWORD=

# Migrate
php artisan migrate --seed
```

---

## Tài Liệu Tham Khảo

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3)
- [PHP 8.3 Documentation](https://www.php.net/manual/en/)
- [MySQL 8 Documentation](https://dev.mysql.com/doc/refman/8.0/en/)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

> **Lưu ý:** Tài liệu này cần được cập nhật khi có thay đổi lớn về kiến trúc hoặc phân công.  
> Thành viên có đề xuất thay đổi phải tạo PR cập nhật `AGENTS.md` và thông báo cho cả nhóm.
