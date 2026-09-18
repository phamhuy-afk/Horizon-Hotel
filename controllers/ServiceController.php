<?php
namespace Controllers;

use Models\Service;
use Models\ServiceOrder;
use Models\Room;
use Config\Database;

class ServiceController {
    private $serviceModel;
    private $serviceOrderModel;
    private $roomModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
            header("Location: index.php?route=login");
            exit;
        }
        $this->serviceModel = new Service();
        $this->serviceOrderModel = new ServiceOrder();
        $this->roomModel = new Room();
    }

    // Trang quản lý Dịch Vụ (CRUD danh mục dịch vụ)
    public function services() {
        $fullname = $_SESSION['fullname'] ?? 'Admin';

        $action = $_GET['action'] ?? ($_POST['action'] ?? '');

        // XỬ LÝ XÓA DỊCH VỤ (Chỉ Admin)
        if ($action === 'delete') {
            if ($_SESSION['role'] !== 'admin') {
                die("Unauthorized access.");
            }
            $id = (int)($_GET['id'] ?? 0);
            $this->serviceModel->delete($id);
            header("Location: index.php?route=services");
            exit;
        }

        // XỬ LÝ THÊM / SỬA DỊCH VỤ (Chỉ Admin)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SESSION['role'] !== 'admin') {
                die("Unauthorized access.");
            }
            $name     = $_POST['service_name'] ?? '';
            $desc     = $_POST['description'] ?? '';
            $price    = (float)($_POST['price'] ?? 0);
            $category = $_POST['category'] ?? 'other';
            $status   = $_POST['status'] ?? 'active';

            if ($action === 'add') {
                $this->serviceModel->create($name, $desc, $price, $category, $status);
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                $this->serviceModel->update($id, $name, $desc, $price, $category, $status);
            }
            header("Location: index.php?route=services");
            exit;
        }

        $services = $this->serviceModel->getAll();

        $categories = [
            'food'  => ['text' => 'Đồ Ăn / Uống', 'icon' => 'fa-utensils', 'color' => '#ef4444'],
            'table' => ['text' => 'Đặt Bàn', 'icon' => 'fa-chair', 'color' => '#f59e0b'],
            'other' => ['text' => 'Khác', 'icon' => 'fa-concierge-bell', 'color' => '#3b82f6']
        ];

        include __DIR__ . '/../views/services.php';
    }

    // Trang quản lý Đơn Dịch Vụ (gọi món từ phòng)
    public function serviceOrders() {
        $fullname = $_SESSION['fullname'] ?? 'Admin';

        $action = $_GET['action'] ?? ($_POST['action'] ?? '');

        // XỬ LÝ XÓA ĐƠN DỊCH VỤ
        if ($action === 'delete') {
            $id = (int)($_GET['id'] ?? 0);
            $this->serviceOrderModel->delete($id);
            header("Location: index.php?route=service_orders");
            exit;
        }

        // XỬ LÝ THÊM / SỬA ĐƠN DỊCH VỤ
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id    = (int)($_POST['room_id'] ?? 0);
            $service_id = (int)($_POST['service_id'] ?? 0);
            $qty        = (int)($_POST['quantity'] ?? 1);
            $status     = $_POST['status'] ?? 'pending';
            $note       = $_POST['note'] ?? '';

            // Tính total_price
            $service = $this->serviceModel->getById($service_id);
            $price = $service ? (float)$service['price'] : 0;
            $total = $price * $qty;

            // Tìm booking_id của phòng đang nhận (checked_in)
            $db = Database::getInstance()->getConnection();
            $b_stmt = $db->prepare("SELECT id FROM bookings WHERE room_id = ? AND status = 'checked_in' LIMIT 1");
            $b_stmt->execute([$room_id]);
            $booking_id = $b_stmt->fetchColumn() ?: 0;

            if ($action === 'add') {
                $this->serviceOrderModel->create($room_id, $booking_id, $service_id, $qty, $total, $status, $note);
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                // Cập nhật thủ công qua db trực tiếp vì update phức tạp hơn
                $upd = $db->prepare("UPDATE service_orders SET room_id=?, booking_id=?, service_id=?, quantity=?, total_price=?, status=?, note=? WHERE id=?");
                $upd->execute([$room_id, $booking_id, $service_id, $qty, $total, $status, $note, $id]);
            }
            header("Location: index.php?route=service_orders");
            exit;
        }

        // XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN DỊCH VỤ
        if ($action === 'update_status') {
            $id = (int)($_GET['id'] ?? 0);
            $new_status = $_GET['new_status'] ?? '';
            $allowed = ['pending', 'processing', 'completed', 'cancelled'];
            if (in_array($new_status, $allowed)) {
                $this->serviceOrderModel->updateStatus($id, $new_status);
            }
            header("Location: index.php?route=service_orders");
            exit;
        }

        $orders = $this->serviceOrderModel->getAll();
        $rooms_list = $this->roomModel->getAll();
        $services_list = $this->serviceModel->getAll('active');

        $status_badges = [
            'pending'    => ['class' => 'badge-warning', 'text' => 'Chờ Xử Lý'],
            'processing' => ['class' => 'badge-primary', 'text' => 'Đang Chuẩn Bị'],
            'completed'  => ['class' => 'badge-success', 'text' => 'Đã Hoàn Thành'],
            'cancelled'  => ['class' => 'badge-secondary', 'text' => 'Đã Hủy']
        ];

        include __DIR__ . '/../views/service_orders.php';
    }
}
