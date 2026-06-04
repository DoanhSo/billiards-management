# AGENTS.md

# Quy Định Phát Triển Dự Án

## Thông Tin Dự Án

**Tên dự án:** Hệ thống quản lý quán Billiards

**Công nghệ sử dụng:**

* Laravel 12
* PHP 8.3+
* MySQL 8+
* Blade Template
* Bootstrap 5
* JavaScript
* Git & GitHub

---

# Mục Tiêu Dự Án

Xây dựng hệ thống web hỗ trợ quản lý hoạt động của quán Billiards bao gồm:

* Quản lý người dùng
* Quản lý bàn chơi
* Quản lý đặt bàn
* Quản lý phiên chơi
* Quản lý sản phẩm
* Quản lý hóa đơn
* Thống kê doanh thu

---

# Kiến Trúc Dự Án

Dự án áp dụng kiến trúc:

Request
→ Controller
→ Service
→ Model (Eloquent)
→ Database

Nguyên tắc:

* Controller chỉ xử lý Request và Response.
* Service chứa toàn bộ Business Logic.
* Model chịu trách nhiệm thao tác dữ liệu và định nghĩa quan hệ.
* Validation được xử lý bằng Form Request.
* Không viết Business Logic trong Controller.
* Không viết truy vấn SQL trực tiếp khi Eloquent hỗ trợ.

---

# Cấu Trúc Thư Mục

app/

├── Http/

│ ├── Controllers/

│ ├── Requests/

│ └── Middleware/

│

├── Models/

│

├── Services/

│ ├── Auth/

│ ├── User/

│ ├── Table/

│ ├── Booking/

│ ├── Session/

│ ├── Invoice/

│ ├── Product/

│ └── Dashboard/

│

└── Providers/

---

# Danh Sách Module

## Auth Module

Chức năng:

* Đăng nhập
* Đăng xuất
* Đổi mật khẩu
* Quên mật khẩu

---

## User Module

Chức năng:

* Quản lý tài khoản
* Quản lý vai trò
* Khóa / Mở khóa tài khoản

---

## Table Module

Chức năng:

* Quản lý bàn chơi
* Quản lý trạng thái bàn

Trạng thái:

* Available
* Reserved
* Playing
* Maintenance

---

## Booking Module

Chức năng:

* Đặt bàn
* Hủy đặt bàn
* Xác nhận đặt bàn
* Lịch sử đặt bàn

---

## Session Module

Chức năng:

* Bắt đầu phiên chơi
* Kết thúc phiên chơi
* Tính thời gian chơi
* Tính tiền bàn

---

## Invoice Module

Chức năng:

* Tạo hóa đơn
* Thanh toán
* Lịch sử hóa đơn

---

## Product Module

Chức năng:

* Quản lý đồ ăn
* Quản lý đồ uống
* Quản lý danh mục sản phẩm

---

## Dashboard Module

Chức năng:

* Thống kê doanh thu
* Thống kê lượt đặt bàn
* Thống kê bàn được sử dụng nhiều nhất
* Thống kê sản phẩm bán chạy

---

# Phân Chia Công Việc

## Thành viên 1

### Module

* Auth
* User

### Nhiệm vụ

* Authentication
* Authorization
* User Management
* Role Management

---

## Thành viên 2

### Module

* Table
* Booking

### Nhiệm vụ

* Quản lý bàn
* Quản lý trạng thái bàn
* Quản lý đặt bàn

---

## Thành viên 3

### Module

* Session
* Invoice

### Nhiệm vụ

* Quản lý phiên chơi
* Tính tiền
* Lập hóa đơn

---

## Thành viên 4

### Module

* Product
* Dashboard

### Nhiệm vụ

* Quản lý sản phẩm
* Thống kê
* Dashboard

---

# Database Tables

1. roles
2. users
3. billiard_tables
4. bookings
5. table_sessions
6. categories
7. products
8. invoices
9. invoice_details

---

# Quy Tắc Database

* Mỗi thành viên chỉ sửa migration thuộc module của mình.
* Không sửa migration đã merge vào develop.
* Tất cả khóa ngoại phải sử dụng foreignId().
* Luôn khai báo relationship trong Model.
* Sử dụng Migration thay vì chỉnh sửa trực tiếp Database.

---

# Quy Tắc Đặt Tên

## Controller

Ví dụ:

* UserController
* BookingController
* ProductController

---

## Service

Ví dụ:

* UserService
* BookingService
* InvoiceService

---

## Request

Ví dụ:

* StoreUserRequest
* UpdateUserRequest
* StoreBookingRequest

---

## Model

Ví dụ:

* User
* Booking
* Product

---

## Method

Sử dụng camelCase.

Ví dụ:

* createBooking()
* updateUser()
* calculateInvoice()

---

## Database

Tên bảng:

* users
* bookings
* products

Tên cột:

* user_id
* table_id
* created_at

---

# Coding Standards

* Tuân thủ PSR-12.
* Mỗi class chỉ thực hiện một nhiệm vụ.
* Không viết query trực tiếp trong Controller.
* Không sử dụng biến tên một ký tự.
* Sử dụng Type Hint đầy đủ.
* Luôn sử dụng Form Request để Validate.
* Ưu tiên Dependency Injection.

Ví dụ:

public function store(StoreBookingRequest $request)

---

# Quy Tắc Controller

Controller chỉ:

* Nhận Request
* Gọi Service
* Trả Response

Không được:

* Viết Business Logic
* Query Database phức tạp

---

# Quy Tắc Service

Service chứa:

* Business Logic
* Tính toán
* Kiểm tra nghiệp vụ

Ví dụ:

* Kiểm tra bàn trống
* Tính tiền giờ chơi
* Tạo hóa đơn

---

# Quy Tắc Model

Model chỉ chứa:

* Relationship
* Scope
* Accessor
* Mutator

Không viết Business Logic lớn trong Model.

---

# Git Workflow

main

│

develop

├── dev-doanh

├── dev-duong

├── dev-hieu

└── dev-minh

---

# Quy Tắc Làm Việc Git

* Không push trực tiếp lên main.
* Không push trực tiếp lên develop.
* Làm việc trên branch cá nhân.
* Sau khi hoàn thành tạo Pull Request vào develop.
* Trưởng nhóm review trước khi merge.
* Resolve toàn bộ conflict trước khi merge.

---

# Quy Tắc Pull Request

Pull Request phải ghi rõ:

* Chức năng đã hoàn thành
* Các file đã sửa
* Database thay đổi (nếu có)

Ví dụ:

[Booking] Hoàn thành chức năng tạo đặt bàn

---

# Quy Tắc Commit

Ví dụ:

[feat] Thêm chức năng đăng nhập

[fix] Sửa lỗi tính tiền bàn

[docs] Cập nhật tài liệu

[refactor] Tối ưu UserService

---

# Kiểm Tra Trước Khi Commit

* Không còn lỗi cú pháp.
* Không còn dd().
* Không còn dump().
* Không commit file .env.
* Đã migrate thành công.
* Đã test chức năng vừa thực hiện.

---

# Công Cụ Sử Dụng

VS Code Extensions:

* PHP Intelephense
* Laravel Blade Snippets
* GitLens
* Thunder Client
* Prettier

---

# Phiên Bản

Laravel: 12.x

PHP: 8.3+

MySQL: 8+

NodeJS: 18+

---

# Tài Liệu Tham Khảo

* Laravel Documentation
* Bootstrap Documentation
* PHP Documentation
* MySQL Documentation
* Git Documentation
