<?php
namespace Controllers;

use Models\Employee;

class AuthController {
    private $employeeModel;

    public function __construct() {
        $this->employeeModel = new Employee();
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?route=dashboard");
            exit;
        }

        $error = '';
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Vui lòng nhập email và mật khẩu.";
            } else {
                $user = $this->employeeModel->getByEmail($email);

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    header("Location: index.php?route=dashboard");
                    exit;
                } else {
                    $error = "Email hoặc mật khẩu không chính xác.";
                }
            }
        }
        include __DIR__ . '/../views/login.php';
    }

    public function register() {
        $error = '';
        $success = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($fullname) || empty($email) || empty($password)) {
                $error = "Vui lòng nhập đầy đủ thông tin.";
            } elseif (strlen($password) < 6) {
                $error = "Mật khẩu phải có ít nhất 6 ký tự.";
            } else {
                $existing = $this->employeeModel->getByEmail($email);
                if ($existing) {
                    $error = "Email này đã được đăng ký.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    if ($this->employeeModel->create($fullname, $email, $hash, 'staff')) {
                        $success = "Đăng ký thành công! Bạn có thể nhấn Đăng nhập bên dưới.";
                    } else {
                        $error = "Đã xảy ra lỗi kết nối, vui lòng thử lại sau.";
                    }
                }
            }
        }
        include __DIR__ . '/../views/register.php';
    }

    public function forgotPassword() {
        $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
        $error = '';
        $success = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if ($step === 2) {
                $email = trim($_POST['email'] ?? '');
                $user = $this->employeeModel->getByEmail($email);
                
                if (!$user) {
                    $error = "Email này chưa được đăng ký trong hệ thống.";
                    $step = 1;
                } else {
                    $_SESSION['reset_email'] = $email;
                }
            } elseif ($step === 3) {
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                $email = $_SESSION['reset_email'] ?? '';
                
                if (empty($email)) {
                    $error = "Phiên làm việc hết hạn. Vui lòng thử lại.";
                    $step = 1;
                } elseif (strlen($new_password) < 6) {
                    $error = "Mật khẩu phải từ 6 ký tự trở lên.";
                    $step = 2;
                } elseif ($new_password !== $confirm_password) {
                    $error = "Mật khẩu xác nhận không khớp.";
                    $step = 2;
                } else {
                    $hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $user = $this->employeeModel->getByEmail($email);
                    if ($user && $this->employeeModel->update($user['id'], $user['fullname'], $email, $user['role'], $hash)) {
                        $success = "Mật khẩu đã được khôi phục thành công! Hãy đăng nhập lại.";
                        unset($_SESSION['reset_email']);
                        $step = 4; // Hoàn thành
                    } else {
                        $error = "Có lỗi xảy ra, vui lòng thử lại sau.";
                        $step = 2;
                    }
                }
            }
        }
        include __DIR__ . '/../views/forgot-password.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?route=login");
        exit;
    }
}
