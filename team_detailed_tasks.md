# 📋 PHÂN CÔNG & ÔN TẬP ĐỒ ÁN MVC - NHÓM 3 NGƯỜI
## Hệ Thống Quản Lý Khách Sạn Horizon Hotel (MVC Architecture Refactoring)

---

## 👤 THÀNH VIÊN 1: KIẾN TRÚC LÕI, CƠ SỞ DỮ LIỆU & XÁC THỰC (Core & Security)
*Phụ trách: Điểm tiếp nhận yêu cầu (Front Controller), Quản lý Kết nối CSDL (Singleton Pattern), và toàn bộ các tính năng Bảo mật/Xác thực tài khoản.*

### 📁 Các File Chịu Trách Nhiệm:
* `index.php` (Front Controller & Bộ định tuyến trung tâm)
* `config/Database.php` (Lớp kết nối cơ sở dữ liệu dạng Singleton)
* `config/db.php` (Cầu nối PDO tương thích ngược cho các API cũ)
* `models/Employee.php` (Lớp Model quản lý dữ liệu người dùng / bảng `users`)
* `controllers/AuthController.php` (Controller xử lý nghiệp vụ Đăng nhập, Đăng ký, Đăng xuất, Quên mật khẩu)
* `views/login.php`, `views/register.php`, `views/forgot-password.php` (Tầng View của các màn hình tương ứng)
* `css/login.css` (Giao diện trang đăng nhập/đăng ký)
* `logout.php` (File chuyển hướng đăng xuất ở gốc)

### 💡 Các Kiến Thức Trọng Tâm Cần Ôn Tập:
1. **Front Controller Pattern:**
   * Cách bộ định tuyến điều phối luồng chạy của chương trình dựa trên tham số `route` (ví dụ: `index.php?route=login`).
   * Cơ chế tự động nạp lớp bằng hàm `spl_autoload_register()`, chuyển đổi namespace và chữ viết hoa/viết thường thành đường dẫn tệp tin thực tế.
2. **Singleton Pattern:**
   * Tại sao phải khai báo thuộc tính static `$instance` và biến hàm khởi dựng `__construct()` thành `private`?
   * Cách phương thức `Database::getInstance()->getConnection()` giúp tái sử dụng kết nối PDO duy nhất, tránh tràn kết nối MySQL.
3. **Mã hóa Mật khẩu:**
   * Giải thích thuật toán `bcrypt` được PHP sử dụng thông qua hàm `password_hash()` khi đăng ký, và cách kiểm tra khớp mật khẩu thông qua `password_verify()` khi đăng nhập.

---

## 👤 THÀNH VIÊN 2: QUẢN LÝ PHÒNG & DỊCH VỤ KHÁCH SẠN (Core Services & Layout System)
*Phụ trách: Quản lý danh sách phòng, danh mục dịch vụ, đơn đặt món/gọi dịch vụ, và hệ thống khung giao diện Dashboard dùng chung.*

### 📁 Các File Chịu Trách Nhiệm:
* `models/Room.php` (Model xử lý bảng phòng `rooms`)
* `models/Service.php` (Model xử lý danh mục dịch vụ `services`)
* `models/ServiceOrder.php` (Model xử lý các đơn gọi món từ phòng `service_orders`)
* `controllers/RoomController.php` (Controller xử lý nghiệp vụ phòng và lọc tìm kiếm)
* `controllers/ServiceController.php` (Controller xử lý nghiệp vụ CRUD dịch vụ và gọi đồ ăn uống)
* `views/rooms.php`, `views/services.php`, `views/service_orders.php` (Tầng hiển thị giao diện danh sách phòng, dịch vụ, gọi món)
* `views/layouts/sidebar.php` (Layout menu điều hướng dùng chung ở cạnh màn hình)
* `css/dashboard.css` (Giao diện tổng quát của trang quản trị)

### 💡 Các Kiến Thức Trọng Tâm Cần Ôn Tập:
1. **Layout & Tái Sử Dụng Mã Nguồn (DRY Principle):**
   * Giải thích cơ chế nhúng Layout `sidebar.php` vào các tệp View để tránh lặp code, đồng thời cách so sánh biến `route` hiện tại để tự động thêm class `active` làm sáng nút menu điều hướng đang chọn.
2. **Prepared Statements (Ngăn chặn SQL Injection):**
   * Tại sao tất cả truy vấn SQL trong các Model đều không nối chuỗi trực tiếp mà dùng tham số hóa (dấu hỏi chấm `?` hoặc `:name`) kết hợp với PDO `prepare()` và `execute()`?
3. **Lưu Trữ Dữ Liệu Dạng Mảng (JSON trong SQL):**
   * Giải thích lý do cột `amenities` trong bảng `rooms` lưu trữ dưới dạng chuỗi JSON thay vì tạo thêm một bảng tiện ích riêng.
   * Cách sử dụng `json_encode()` để chuyển mảng các tiện ích từ form thành chuỗi lưu vào CSDL, và `json_decode()` để hiển thị các icon tiện ích tương ứng trên giao diện.

---

## 👤 THÀNH VIÊN 3: ĐẶT PHÒNG, KHÁCH HÀNG & THỐNG KÊ DOANH THU (Workflows & Analytics)
*Phụ trách: Quy trình đặt phòng (Bookings), quản lý cơ sở dữ liệu khách hàng, hóa đơn thanh toán, và các thuật toán tính toán thống kê.*

### 📁 Các File Chịu Trách Nhiệm:
* `models/Booking.php` (Model xử lý bảng đặt phòng `bookings`)
* `models/Customer.php` (Model xử lý bảng khách hàng `customers`)
* `controllers/BookingController.php` (Controller xử lý luồng đặt phòng, hủy phòng, và hiển thị hóa đơn)
* `controllers/CustomerController.php` (Controller xử lý danh sách khách hàng và tự động tăng số lần đặt)
* `controllers/DashboardController.php` (Controller tính toán các chỉ số thống kê trên Dashboard)
* `views/bookings.php`, `views/customers.php`, `views/dashboard.php`, `views/invoice.php` (Tầng View giao diện đặt phòng, khách hàng, thống kê và hóa đơn)

### 💡 Các Kiến Thức Trọng Tâm Cần Ôn Tập:
1. **Luồng Nghiệp Vụ Đặt Phòng & Đồng Bộ Trạng Thái Phòng:**
   * Trình bày quy trình từ lúc khách hàng được đặt phòng (`pending`/`confirmed` -> phòng chuyển sang **Đang thuê - rented**), đến lúc check-out trả phòng (phòng chuyển sang **Còn trống - available**).
   * Giải thích phương thức cập nhật lại trạng thái phòng cũ và mới trong `BookingController::index()` khi người dùng thực hiện cập nhật (sửa) thông tin đơn đặt phòng từ phòng này sang phòng khác.
2. **Truy vấn Kết bảng (SQL JOINs):**
   * Giải thích cách kết nối 3 bảng dữ liệu (`bookings`, `customers`, `rooms`) thông qua truy vấn `LEFT JOIN` trong hàm `Booking::getAll()` để hiển thị tên khách hàng và tên phòng thay vì chỉ hiển thị ID.
3. **Thuật Toán Phân Tích & Vẽ Biểu Đồ:**
   * Cách hàm `DashboardController::index()` lặp qua 12 tháng, lọc các hóa đơn đặt phòng và gọi dịch vụ tương ứng với từng tháng để tính ra tổng doanh thu và tỉ lệ lấp đầy phòng trung bình trong năm.
   * Cách dữ liệu số học đó được chuyển đổi thành biến Javascript để thư viện `Chart.js` hiển thị lên biểu đồ cột và đường.
4. **Tính Toán Hóa Đơn (`invoice`):**
   * Quy trình tính toán giá hóa đơn: lấy tổng tiền phòng (dựa theo số đêm) cộng với tổng tiền các dịch vụ đã gọi ở trạng thái hợp lệ (`completed`/`processing`) để ra tổng tiền cuối cùng cần thanh toán.
