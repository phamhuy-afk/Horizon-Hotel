<?php
namespace Models;

use Config\Database;
use PDO;

class ServiceOrder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = '') {
        $sql = "SELECT so.*, 
                       r.room_name, 
                       s.service_name, 
                       s.price as service_price, 
                       s.category as service_category,
                       b.guest_name
                FROM service_orders so
                LEFT JOIN rooms r ON so.room_id = r.id
                LEFT JOIN services s ON so.service_id = s.id
                LEFT JOIN bookings b ON so.booking_id = b.id
                WHERE 1=1";
        $params = [];
        if ($status !== '') {
            $sql .= " AND so.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY so.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByBookingId($booking_id) {
        $stmt = $this->db->prepare("
            SELECT so.*, s.service_name, s.price as service_price
            FROM service_orders so
            JOIN services s ON so.service_id = s.id
            WHERE so.booking_id = ? AND so.status != 'cancelled'
        ");
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM service_orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($room_id, $booking_id, $service_id, $quantity, $total_price, $status, $note) {
        $stmt = $this->db->prepare("INSERT INTO service_orders (room_id, booking_id, service_id, quantity, total_price, status, note) VALUES (?,?,?,?,?,?,?)");
        return $stmt->execute([$room_id, $booking_id, $service_id, $quantity, $total_price, $status, $note]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE service_orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM service_orders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getMonthlyRevenue($month, $year) {
        $stmt = $this->db->prepare("SELECT SUM(total_price) FROM service_orders WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? AND status != 'cancelled'");
        $stmt->execute([$month, $year]);
        return (float) ($stmt->fetchColumn() ?: 0);
    }
}
