<?php
namespace Controllers;

use Models\Employee;

class EmployeeController {
    private $employeeModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?route=login");
            exit;
        }
        $this->employeeModel = new Employee();
    }

    public function index() {
        $fullname_admin = $_SESSION['fullname'] ?? 'Admin';

        $action = $_GET['action'] ?? ($_POST['action'] ?? '');

        // XỬ LÝ XÓA NHÂN VIÊN
        if ($action === 'delete') {
            $delete_id = (int)($_GET['id'] ?? 0);
            // Không cho phép tự xóa chính mình
            if ($delete_id === (int)$_SESSION['user_id']) {
                $_SESSION['flash_msg'] = [
                    'type' => 'error', 
                    'bg' => '#fee2e2', 
                    'color' => '#ef4444', 
                    'text' => 'Bạn không thể tự xóa tài khoản của chính mình!'
                ];
            } else {
                $this->employeeModel->delete($delete_id);
                $_SESSION['flash_msg'] = [
                    'type' => 'success', 
                    'bg' => '#ecfdf5', 
                    'color' => '#10b981', 
                    'text' => 'Đã xóa tài khoản thành công!'
                ];
            }
            header("Location: index.php?route=employees");
            exit;
        }

        // XỬ LÝ THÊM / SỬA NHÂN VIÊN
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname    = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role     = $_POST['role'] ?? 'staff';

            if ($action === 'add') {
                // Kiểm tra email trùng
                $existing = $this->employeeModel->getByEmail($email);
                if ($existing) {
                    $_SESSION['flash_msg'] = [
                        'type' => 'error', 
                        'bg' => '#fee2e2', 
                        'color' => '#ef4444', 
                        'text' => 'Email này đã tồn tại trong hệ thống!'
                    ];
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->employeeModel->create($fname, $email, $hash, $role);
                    $_SESSION['flash_msg'] = [
                        'type' => 'success', 
                        'bg' => '#ecfdf5', 
                        'color' => '#10b981', 
                        'text' => 'Thêm nhân viên mới thành công!'
                    ];
                }
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->employeeModel->update($id, $fname, $email, $role, $hash);
                } else {
                    $this->employeeModel->update($id, $fname, $email, $role);
                }
                $_SESSION['flash_msg'] = [
                    'type' => 'success', 
                    'bg' => '#ecfdf5', 
                    'color' => '#10b981', 
                    'text' => 'Cập nhật thông tin nhân viên thành công!'
                ];
            }
            header("Location: index.php?route=employees");
            exit;
        }

        $employees = $this->employeeModel->getAll();

        include __DIR__ . '/../views/employees.php';
    }
}
