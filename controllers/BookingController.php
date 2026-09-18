<?php
namespace Controllers;

use Models\Booking;
use Models\Room;
use Models\Customer;
use Models\ServiceOrder;

class BookingController {
    private $bookingModel;
    private $roomModel;
    private $customerModel;
    private $serviceOrderModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
            header("Location: index.php?route=login");
            exit;
        }
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->customerModel = new Customer();
        $this->serviceOrderModel = new ServiceOrder();
    }

    public function index() {
        $fullname = $_SESSION['fullname'] ?? 'Admin';

        $action = $_GET['action'] ?? ($_POST['action'] ?? '');

        // XỬ LÝ XÓA ĐẶT PHÒNG
        if ($action === 'delete') {
            $delete_id = (int)$_GET['id'];
            $old_booking = $this->bookingModel->getById($delete_id);
            
            $this->bookingModel->delete($delete_id);
            
            if ($old_booking && $old_booking['room_id']) {
                $this->roomModel->updateStatus($old_booking['room_id'], 'available');
            }
            header("Location: index.php?route=bookings");
            exit;
        }

        // XỬ LÝ CẬP NHẬT TRẠNG THÁI TRỰC TIẾP
        if ($action === 'update_status') {
            $status_id = (int)$_GET['id'];
            $new_status = $_GET['new_status'];
            $allowed = ['pending', 'confirmed', 'checked_in', 'cancelled', 'checked_out'];
            if (in_array($new_status, $allowed)) {
                $this->bookingModel->updateStatus($status_id, $new_status);
                
                $old_booking = $this->bookingModel->getById($status_id);
                if ($old_booking && $old_booking['room_id']) {
                    $r_status = ($new_status === 'confirmed' || $new_status === 'checked_in') ? 'rented' : 'available';
                    $this->roomModel->updateStatus($old_booking['room_id'], $r_status);
                }
            }
            header("Location: index.php?route=bookings");
            exit;
        }

        // XỬ LÝ THÊM / SỬA ĐẶT PHÒNG
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = (int)($_POST['customer_id'] ?? 0);
            $guest_name  = trim($_POST['guest_name'] ?? '');
            $room_id     = (int)($_POST['room_id'] ?? 0);
            $room_name   = trim($_POST['room_name'] ?? '');
            $check_in    = $_POST['check_in'] ?? '';
            $check_out   = $_POST['check_out'] ?? '';
            $guests      = (int)($_POST['guests'] ?? 1);
            $total_price = (float)($_POST['total_price'] ?? 0);
            $status      = $_POST['status'] ?? 'pending';
            $payment_status = $_POST['payment_status'] ?? 'unpaid';
            $note        = trim($_POST['note'] ?? '');
            $phone       = trim($_POST['phone'] ?? '');

            if ($action === 'add') {
                $this->bookingModel->create($customer_id, $guest_name, $phone, $room_id, $room_name, $check_in, $check_out, $guests, $total_price, $status, $payment_status, $note);
                // Tăng tổng đặt phòng cho khách hàng
                if ($customer_id) {
                    $this->customerModel->incrementBookings($customer_id);
                }
                // Cập nhật trạng thái phòng
                if ($room_id) {
                    $r_status = ($status === 'confirmed' || $status === 'checked_in') ? 'rented' : 'available';
                    $this->roomModel->updateStatus($room_id, $r_status);
                }
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                
                // Lấy room_id cũ 
                $old_booking = $this->bookingModel->getById($id);
                $old_room = $old_booking ? $old_booking['room_id'] : 0;

                $this->bookingModel->update($id, $customer_id, $guest_name, $phone, $room_id, $room_name, $check_in, $check_out, $guests, $total_price, $status, $payment_status, $note);
                
                // Cập nhật lại trạng thái phòng cũ và mới
                if ($old_room && $old_room != $room_id) {
                    $this->roomModel->updateStatus($old_room, 'available');
                }
                if ($room_id) {
                    $r_status = ($status === 'confirmed' || $status === 'checked_in') ? 'rented' : 'available';
                    $this->roomModel->updateStatus($room_id, $r_status);
                }
            } elseif ($action === 'delete_multiple') {
                $ids = $_POST['ids'] ?? [];
                if (!empty($ids)) {
                    // Cập nhật trạng thái phòng trước khi xóa đặt phòng
                    foreach ($ids as $booking_id) {
                        $b = $this->bookingModel->getById($booking_id);
                        if ($b && $b['room_id']) {
                            $this->roomModel->updateStatus($b['room_id'], 'available');
                        }
                    }
                    $this->bookingModel->deleteMultiple($ids);
                }
            }
            header("Location: index.php?route=bookings");
            exit;
        }

        // Lấy bộ lọc trạng thái
        $status_filter = $_GET['status'] ?? '';
        $bookings = $this->bookingModel->getAll($status_filter);

        // Lấy danh sách phòng trống phục vụ form đặt phòng
        $rooms_list = $this->roomModel->getAll('', 'available');

        // Lấy danh sách khách hàng cho dropdown
        $customers_list = $this->customerModel->getAll();

        $badge_map = [
            'checked_in' => ['class' => 'booking-badge-checkin', 'text' => 'Đã Nhận Phòng'],
            'confirmed'  => ['class' => 'booking-badge-confirmed', 'text' => 'Đã Xác Nhận'],
            'pending'    => ['class' => 'booking-badge-pending', 'text' => 'Chờ Xác Nhận'],
            'cancelled'  => ['class' => 'booking-badge-cancelled', 'text' => 'Đã Hủy'],
            'checked_out' => ['class' => 'booking-badge-checkout', 'text' => 'Đã Trả Phòng'],
        ];

        include __DIR__ . '/../views/bookings.php';
    }

    public function invoice() {
        if (!isset($_GET['id'])) {
            die("Thiếu ID đặt phòng.");
        }

        $id = (int)$_GET['id'];
        $booking = $this->bookingModel->getById($id);

        if (!$booking) {
            die("Không tìm thấy thông tin đặt phòng.");
        }

        // Kiểm tra quyền (chỉ admin hoặc đúng khách hàng đó, nhưng vì không còn customer role, chỉ có admin/staff mới truy cập)
        // Staff/Admin đều được xem hóa đơn
        if (!in_array($_SESSION['role'], ['admin', 'staff'])) {
            die("Bạn không có quyền xem hóa đơn này.");
        }

        // Xử lý xác nhận thanh toán
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'pay') {
            $this->bookingModel->updatePaymentStatus($id, 'paid');
            header("Location: index.php?route=invoice&id=" . $id . "&msg=paid");
            exit;
        }

        // Lấy danh sách dịch vụ đã đặt cho phòng này
        $services = $this->serviceOrderModel->getByBookingId($id);

        $total_services = 0;
        $confirmed_services_total = 0;
        
        foreach ($services as $s) {
            $total_services += $s['total_price'];
            if ($s['status'] === 'processing' || $s['status'] === 'completed') {
                $confirmed_services_total += $s['total_price'];
            }
        }

        $is_final = ($booking['status'] === 'checked_out');
        $title_text = $is_final ? "HÓA ĐƠN THANH TOÁN" : "HÓA ĐƠN TẠM TÍNH";

        $grand_total_display = $booking['total_price'] + $total_services;
        $is_room_paid = ($booking['payment_status'] === 'paid');

        $balance = 0;
        if (!$is_room_paid) {
            $balance = $grand_total_display;
        } else {
            $balance = 0;
        }

        $is_fully_paid = ($balance == 0 && $is_room_paid);

        include __DIR__ . '/../views/invoice.php';
    }
}
