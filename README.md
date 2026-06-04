# README.md

# HỆ THỐNG QUẢN LÝ QUÁN BILLIARDS

## Giới Thiệu

Hệ thống quản lý quán Billiards là một ứng dụng web được xây dựng nhằm hỗ trợ quản lý hoạt động của quán billiards, bao gồm:

* Quản lý người dùng
* Quản lý bàn chơi
* Quản lý đặt bàn
* Quản lý phiên chơi
* Quản lý sản phẩm
* Quản lý hóa đơn
* Thống kê doanh thu

Dự án được phát triển trong khuôn khổ môn Công Nghệ Web.

---

# Thành Viên Nhóm

| STT | Họ và tên  | Vai trò     |
| --- | ---------- | ----------- |
| 1   | Nguyễn Quốc Doanh | Trưởng nhóm |
| 2   | Nguyễn Thái Dương | Thành viên  |
| 3   | Lê Hữu Hiệu | Thành viên  |
| 4   | Thân Đức Minh | Thành viên  |

---

# Công Nghệ Sử Dụng

## Backend

* Laravel 12
* PHP 8.3+

## Frontend

* Blade Template
* Bootstrap 5
* JavaScript

## Database

* MySQL 8+

## Công Cụ

* Git
* GitHub
* VS Code
* XAMPP

---

# Cấu Trúc Dự Án

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Middleware/
│
├── Models/
│
├── Services/
│   ├── Auth/
│   ├── User/
│   ├── Table/
│   ├── Booking/
│   ├── Session/
│   ├── Invoice/
│   ├── Product/
│   └── Dashboard/
│
└── Providers/

database/
├── migrations/
├── seeders/

resources/
├── views/
├── css/
└── js/

routes/
├── web.php
└── api.php
```

---

# Các Module Chức Năng

## Auth

* Đăng nhập
* Đăng xuất
* Đổi mật khẩu

---

## User

* Quản lý tài khoản
* Quản lý vai trò

---

## Table

* Quản lý bàn chơi
* Quản lý trạng thái bàn

---

## Booking

* Đặt bàn
* Hủy đặt bàn
* Xem lịch sử đặt bàn

---

## Session

* Bắt đầu phiên chơi
* Kết thúc phiên chơi
* Tính thời gian chơi

---

## Invoice

* Tạo hóa đơn
* Thanh toán
* Xem lịch sử hóa đơn

---

## Product

* Quản lý đồ ăn
* Quản lý đồ uống

---

## Dashboard

* Thống kê doanh thu
* Thống kê lượt đặt bàn
* Thống kê sản phẩm bán chạy

---

# Thiết Kế Cơ Sở Dữ Liệu

## Danh sách bảng

* roles
* users
* billiard_tables
* bookings
* table_sessions
* categories
* products
* invoices
* invoice_details

---

# Yêu Cầu Hệ Thống

* PHP >= 8.3
* Composer >= 2.x
* MySQL >= 8
* NodeJS >= 18
* NPM >= 9

---

# Hướng Dẫn Cài Đặt

## Clone Source Code

```bash
git clone <repository-url>
```

```bash
cd billiards-management
```

---

## Cài Đặt Dependency

```bash
composer install
```

```bash
npm install
```

---

## Tạo File Môi Trường

```bash
cp .env.example .env
```

---

## Tạo App Key

```bash
php artisan key:generate
```

---

## Cấu Hình Database

Mở file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billiard_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Chạy Migration

```bash
php artisan migrate
```

---

## Chạy Seeder

```bash
php artisan db:seed
```

---

## Build Frontend

```bash
npm run dev
```

---

## Chạy Dự Án

```bash
php artisan serve
```

Mở trình duyệt:

```text
http://127.0.0.1:8000
```

---

# Git Workflow

```text
main
│
develop
├── dev-doanh
├── dev-duong
├── dev-hieu
└── dev-minh
```

Quy tắc:

* Không push trực tiếp lên main.
* Làm việc trên branch cá nhân.
* Tạo Pull Request vào develop.
* Trưởng nhóm review trước khi merge.

---

# Coding Convention

* Tuân thủ PSR-12.
* Controller không chứa business logic.
* Service xử lý nghiệp vụ.
* Sử dụng Form Request để validate.
* Sử dụng Eloquent ORM.

---

# Tài Liệu Liên Quan

* AGENTS.md
* OUTLINE.md

---

# Giấy Phép

Dự án được phát triển phục vụ mục đích học tập và nghiên cứu tại Trường Đại học Thủy Lợi.
