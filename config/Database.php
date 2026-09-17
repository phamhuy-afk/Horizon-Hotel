<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $conn;

    private $host = '127.0.0.1';
    private $port = '3306';
    private $dbname = 'quanlyks';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            // Đầu tiên kết nối để tạo CSDL nếu chưa có
            $setup_conn = new PDO("mysql:host={$this->host};port={$this->port};charset=utf8mb4", $this->username, $this->password);
            $setup_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $setup_conn->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Kết nối đến CSDL chính
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Gọi hàm khởi tạo bảng nếu cần
            $this->initializeTables();
        } catch (PDOException $e) {
            die("<div style='color:red; font-weight:bold; padding:20px;'>Lỗi kết nối CSDL MySQL (Port 3306): " . $e->getMessage() . "</div>");
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    private function initializeTables() {
        // Tạo bảng users
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fullname` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `role` enum('staff','admin') DEFAULT 'staff',
            `created_at` timestamp DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tạo bảng rooms
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `rooms` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `room_name` varchar(50) NOT NULL,
            `room_type` varchar(50) NOT NULL,
            `capacity` int(11) NOT NULL DEFAULT 2,
            `price` int(11) NOT NULL,
            `status` enum('available', 'rented', 'maintenance') DEFAULT 'available',
            `amenities` text DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tạo bảng bookings
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `bookings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `customer_id` int(11) DEFAULT NULL,
            `guest_name` varchar(100) NOT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `room_id` int(11) NOT NULL,
            `room_name` varchar(255) NOT NULL,
            `check_in` date NOT NULL,
            `check_out` date NOT NULL,
            `guests` int(11) NOT NULL DEFAULT 1,
            `total_price` decimal(12,2) NOT NULL DEFAULT 0,
            `status` enum('pending','confirmed','checked_in','cancelled','checked_out') DEFAULT 'pending',
            `payment_method` varchar(50) DEFAULT NULL,
            `payment_status` varchar(20) DEFAULT 'unpaid',
            `note` text DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tạo bảng customers
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `customers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fullname` varchar(100) NOT NULL,
            `email` varchar(100) DEFAULT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `type` enum('normal','vip') DEFAULT 'normal',
            `total_bookings` int(11) NOT NULL DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tạo bảng services
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `services` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `service_name` varchar(100) NOT NULL,
            `description` text DEFAULT NULL,
            `price` decimal(12,2) NOT NULL DEFAULT 0,
            `category` enum('food', 'table', 'other') DEFAULT 'other',
            `status` enum('active', 'inactive') DEFAULT 'active',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tạo bảng service_orders
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `service_orders` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `room_id` int(11) NOT NULL,
            `booking_id` int(11) NOT NULL,
            `service_id` int(11) NOT NULL,
            `quantity` int(11) NOT NULL DEFAULT 1,
            `total_price` decimal(12,2) NOT NULL DEFAULT 0,
            `status` enum('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
            `note` text DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tạo bảng reviews
        $this->conn->exec("
        CREATE TABLE IF NOT EXISTS `reviews` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `booking_id` int(11) NOT NULL,
            `customer_id` int(11) NOT NULL,
            `room_id` int(11) NOT NULL,
            `rating` int(11) NOT NULL,
            `comment` text DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Đảm bảo các cột mới tồn tại trong bảng bookings nếu bảng đã được tạo từ trước
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN customer_id int(11) DEFAULT NULL"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN phone varchar(20) DEFAULT NULL"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN guests int(11) NOT NULL DEFAULT 1"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN payment_method varchar(50) DEFAULT NULL"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN payment_status varchar(20) DEFAULT 'unpaid'"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN note text DEFAULT NULL"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE bookings ADD COLUMN created_at timestamp DEFAULT CURRENT_TIMESTAMP"); } catch(\Exception $e) {}

        // Đảm bảo các cột mới tồn tại trong bảng customers nếu bảng đã được tạo từ trước
        try { $this->conn->exec("ALTER TABLE customers ADD COLUMN type enum('normal','vip') DEFAULT 'normal'"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE customers ADD COLUMN total_bookings int(11) NOT NULL DEFAULT 0"); } catch(\Exception $e) {}
        try { $this->conn->exec("ALTER TABLE customers ADD COLUMN created_at timestamp DEFAULT CURRENT_TIMESTAMP"); } catch(\Exception $e) {}
        
        // Đảm bảo bảng service_orders có cột booking_id
        try { $this->conn->exec("ALTER TABLE service_orders ADD COLUMN booking_id int(11) NOT NULL AFTER room_id"); } catch(\Exception $e) {}
    }
}
