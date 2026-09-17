<?php
/**
 * Front Controller - Bộ định tuyến trung tâm của hệ thống Horizon Hotel MVC
 * Pattern: Front Controller + MVC
 * 
 * URL: index.php?route=<tên_trang>[&action=<hành_động>][&id=<id>]
 */

session_start();

// Autoloader đơn giản theo namespace
spl_autoload_register(function ($class) {
    // Chuyển namespace thành đường dẫn file
    // Ví dụ: Controllers\RoomController -> controllers/RoomController.php
    $base_dir = __DIR__ . '/';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    // Xử lý namespace chữ thường trong đường dẫn
    $file = preg_replace_callback('/^(.+\/)([^\/]+\.php)$/', function($m) {
        return strtolower($m[1]) . $m[2];
    }, $file);
    if (file_exists($file)) {
        require_once $file;
    }
});

// Bộ Định Tuyến (Router)
$route = $_GET['route'] ?? 'login';

// Các route không yêu cầu đăng nhập
$public_routes = ['login', 'register', 'forgot-password', 'logout'];

// Nếu chưa đăng nhập và route không phải public -> redirect về login
if (!isset($_SESSION['user_id']) && !in_array($route, $public_routes)) {
    header("Location: index.php?route=login");
    exit;
}

// Nếu đã đăng nhập mà truy cập login/register -> redirect về dashboard
if (isset($_SESSION['user_id']) && in_array($route, ['login', 'register'])) {
    header("Location: index.php?route=dashboard");
    exit;
}

// Ánh Xạ Route đến Controller và Method
switch ($route) {
    // Auth
    case 'login':
        $controller = new Controllers\AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new Controllers\AuthController();
        $controller->register();
        break;

    case 'forgot-password':
        $controller = new Controllers\AuthController();
        $controller->forgotPassword();
        break;

    case 'logout':
        $controller = new Controllers\AuthController();
        $controller->logout();
        break;

    // Dashboard
    case 'dashboard':
        $controller = new Controllers\DashboardController();
        $controller->index();
        break;

    // Rooms
    case 'rooms':
        $controller = new Controllers\RoomController();
        $controller->index();
        break;

    // Bookings
    case 'bookings':
        $controller = new Controllers\BookingController();
        $controller->index();
        break;

    // Invoice
    case 'invoice':
        $controller = new Controllers\BookingController();
        $controller->invoice();
        break;

    // Customers
    case 'customers':
        $controller = new Controllers\CustomerController();
        $controller->index();
        break;

    // Employees (Admin only)
    case 'employees':
        $controller = new Controllers\EmployeeController();
        $controller->index();
        break;

    // Services
    case 'services':
        $controller = new Controllers\ServiceController();
        $controller->services();
        break;

    // Service Orders
    case 'service_orders':
        $controller = new Controllers\ServiceController();
        $controller->serviceOrders();
        break;

    // Route không tồn tại
    default:
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?route=dashboard");
        } else {
            header("Location: index.php?route=login");
        }
        exit;
}
