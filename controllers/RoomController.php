<?php
namespace Controllers;

use Models\Room;

class RoomController {
    private $roomModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
            header("Location: index.php?route=login");
            exit;
        }
        $this->roomModel = new Room();
    }

    public function index() {
        $fullname = $_SESSION['fullname'] ?? 'Admin';

        // Xử lý hành động qua GET/POST
        $action = $_GET['action'] ?? ($_POST['action'] ?? '');

        if ($action === 'delete') {
            if ($_SESSION['role'] !== 'admin') { 
                $_SESSION['flash_msg'] = ['type' => 'error', 'bg' => '#fee2e2', 'color' => '#ef4444', 'text' => 'Bạn không có quyền xóa phòng!'];
            } else {
                $id = (int)($_GET['id'] ?? 0);
                $this->roomModel->delete($id);
            }
            header("Location: index.php?route=rooms");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_name = $_POST['room_name'] ?? '';
            $room_type = $_POST['room_type'] ?? '';
            $capacity  = (int)($_POST['capacity'] ?? 2);
            $price     = (int)($_POST['price']    ?? 0);
            $status    = $_POST['status']   ?? 'available';
            $amenities = isset($_POST['amenities']) ? json_encode($_POST['amenities']) : '[]';

            if ($action === 'add') {
                if ($_SESSION['role'] !== 'admin') { 
                    die("Unauthorized access."); 
                }
                $this->roomModel->create($room_name, $room_type, $capacity, $price, $status, $amenities);
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                if ($_SESSION['role'] === 'admin') {
                    $this->roomModel->update($id, $room_name, $room_type, $capacity, $price, $status, $amenities);
                } else {
                    // Staff chỉ được sửa trạng thái
                    $this->roomModel->updateStatus($id, $status);
                }
            }
            header("Location: index.php?route=rooms");
            exit;
        }

        // Lấy dữ liệu lọc/tìm kiếm
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status'] ?? '';

        $rooms = $this->roomModel->getAll($search, $status_filter);

        include __DIR__ . '/../views/rooms.php';
    }
}
