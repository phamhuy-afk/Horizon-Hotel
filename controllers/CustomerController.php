<?php
namespace Controllers;

use Models\Customer;

class CustomerController {
    private $customerModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
            header("Location: index.php?route=login");
            exit;
        }
        $this->customerModel = new Customer();
    }

    public function index() {
        $fullname = $_SESSION['fullname'] ?? 'Admin';

        $action = $_GET['action'] ?? ($_POST['action'] ?? '');

        // XỬ LÝ XÓA KHÁCH HÀNG
        if ($action === 'delete') {
            $id = (int)($_GET['id'] ?? 0);
            $this->customerModel->delete($id);
            header("Location: index.php?route=customers");
            exit;
        }

        // XỬ LÝ THÊM/SỬA KHÁCH HÀNG
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname    = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');
            $address  = trim($_POST['address'] ?? '');
            $type     = $_POST['type'] ?? 'normal';

            if ($action === 'add') {
                $this->customerModel->create($fname, $email, $phone, $address, $type);
            } elseif ($action === 'edit') {
                $id = (int)($_POST['id'] ?? 0);
                $this->customerModel->update($id, $fname, $email, $phone, $address, $type);
            }
            header("Location: index.php?route=customers");
            exit;
        }

        $customers = $this->customerModel->getAll();

        include __DIR__ . '/../views/customers.php';
    }
}
