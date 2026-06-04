# OUTLINE.md

# HỆ THỐNG QUẢN LÝ QUÁN BILLIARDS

## 1. Giới Thiệu Đề Tài

### Tên đề tài

Hệ thống quản lý quán Billiards

### Mục tiêu

Xây dựng hệ thống web hỗ trợ quản lý và vận hành quán Billiards, giúp:

* Quản lý người dùng
* Quản lý bàn chơi
* Quản lý đặt bàn
* Quản lý phiên chơi
* Quản lý sản phẩm
* Quản lý hóa đơn
* Thống kê doanh thu

Hệ thống giúp giảm thao tác thủ công, tăng hiệu quả quản lý và hỗ trợ theo dõi hoạt động kinh doanh.

---

# 2. Phạm Vi Dự Án

## Trong phạm vi

* Đăng nhập hệ thống
* Phân quyền người dùng
* Quản lý tài khoản
* Quản lý bàn Billiards
* Quản lý đặt bàn
* Quản lý phiên chơi
* Quản lý sản phẩm
* Quản lý hóa đơn
* Thống kê doanh thu

## Ngoài phạm vi

* Thanh toán trực tuyến
* Ứng dụng Mobile
* AI Chatbot
* Hệ thống thông báo thời gian thực

---

# 3. Đối Tượng Sử Dụng

## Admin

Quyền:

* Quản lý người dùng
* Quản lý bàn
* Quản lý sản phẩm
* Quản lý hóa đơn
* Xem thống kê

---

## Nhân viên

Quyền:

* Quản lý đặt bàn
* Bắt đầu phiên chơi
* Kết thúc phiên chơi
* Tạo hóa đơn

---

## Khách hàng

Quyền:

* Đăng nhập
* Đặt bàn
* Xem lịch sử đặt bàn

---

# 4. Các Chức Năng Chính

## 4.1 Authentication Module

Chức năng:

* Đăng nhập
* Đăng xuất
* Đổi mật khẩu
* Phân quyền

---

## 4.2 User Management Module

Chức năng:

* Xem danh sách người dùng
* Thêm người dùng
* Cập nhật người dùng
* Xóa người dùng
* Gán vai trò

---

## 4.3 Table Management Module

Chức năng:

* Thêm bàn
* Cập nhật bàn
* Xóa bàn
* Xem trạng thái bàn

Trạng thái:

* Available
* Reserved
* Playing
* Maintenance

---

## 4.4 Booking Management Module

Chức năng:

* Tạo đặt bàn
* Xác nhận đặt bàn
* Hủy đặt bàn
* Xem lịch sử đặt bàn

---

## 4.5 Playing Session Module

Chức năng:

* Bắt đầu phiên chơi
* Kết thúc phiên chơi
* Tính thời gian chơi
* Tính chi phí bàn

---

## 4.6 Product Management Module

Chức năng:

* Quản lý đồ ăn
* Quản lý đồ uống
* Quản lý danh mục sản phẩm

---

## 4.7 Invoice Management Module

Chức năng:

* Tạo hóa đơn
* Quản lý thanh toán
* Chi tiết hóa đơn
* Lịch sử hóa đơn

---

## 4.8 Dashboard Module

Chức năng:

* Thống kê doanh thu
* Thống kê lượt đặt bàn
* Thống kê sản phẩm bán chạy
* Thống kê bàn được sử dụng nhiều nhất

---

# 5. Thiết Kế Cơ Sở Dữ Liệu

## Danh sách bảng

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

# 6. Quan Hệ Dữ Liệu (ERD)

roles

↓

users

↓

bookings

↓

billiard_tables

billiard_tables

↓

table_sessions

↓

invoices

↓

invoice_details

↓

products

↓

categories

---

# 7. Kiến Trúc Hệ Thống

## Presentation Layer

* Blade Views
* Bootstrap 5

## Application Layer

* Controllers
* Services
* Requests

## Data Layer

* Eloquent Models
* MySQL Database

---

## Luồng xử lý

User

↓

Request

↓

Controller

↓

Service

↓

Model

↓

Database

---

# 8. Yêu Cầu Phi Chức Năng

## Hiệu năng

* Thời gian phản hồi dưới 3 giây.
* Hỗ trợ đồng thời nhiều người dùng.

## Bảo mật

* Mật khẩu được mã hóa bằng bcrypt.
* Phân quyền theo vai trò.
* Chống truy cập trái phép.

## Khả năng bảo trì

* Code theo chuẩn PSR-12.
* Áp dụng Service Layer.
* Dễ mở rộng chức năng.

---

# 9. Phân Công Thành Viên

## Thành viên 1 - Nguyễn Quốc Doanh

Module:

* Auth
* User
* Dashboard

---

## Thành viên 2 - Nguyễn Thái Dương 

Module:

* Table
* Booking

---

## Thành viên 3 - Lê Hữu Hiệu

Module:

* Session
* Invoice

---

## Thành viên 4 - Thân Đức Minh

Module:

* Product 
* Category

---

# 10. Kế Hoạch Thực Hiện

## Tuần 1

* Phân tích yêu cầu
* Thiết kế Database
* Thiết kế giao diện

## Tuần 2

* Auth Module
* User Module

## Tuần 3

* Table Module
* Booking Module

## Tuần 4

* Session Module
* Invoice Module

## Tuần 5

* Product Module
* Dashboard Module

## Tuần 6

* Kiểm thử
* Sửa lỗi
* Hoàn thiện báo cáo

---

# 11. Công Nghệ Sử Dụng

Backend:

* Laravel 12
* PHP 8.3+

Frontend:

* Blade
* Bootstrap 5
* JavaScript

Database:

* MySQL

Version Control:

* Git
* GitHub

---

# 12. Kết Quả Mong Đợi

* Website hoạt động ổn định.
* Giao diện thân thiện với người dùng.
* Quản lý bàn hiệu quả.
* Hỗ trợ tính tiền chính xác.
* Dashboard thống kê trực quan.
* Dễ dàng bảo trì và mở rộng trong tương lai.
