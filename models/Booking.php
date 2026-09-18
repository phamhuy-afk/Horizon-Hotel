<?php
namespace Models;

use Config\Database;
use PDO;

class Booking {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = '') {
        $sql = "SELECT b.*, 
            COALESCE(c.fullname, b.guest_name) AS display_name,
            COALESCE(c.phone, b.phone) AS display_phone,
            c.id AS cust_id,
            COALESCE(r.room_name, b.room_name) AS room_name
            FROM bookings b 
            LEFT JOIN customers c ON b.customer_id = c.id 
            LEFT JOIN rooms r ON b.room_id = r.id
            WHERE 1=1";
        $params = [];
        if ($status !== '') {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY b.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT b.*, r.room_name, r.room_type, r.price as room_price, c.fullname as cust_name FROM bookings b LEFT JOIN rooms r ON b.room_id = r.id LEFT JOIN customers c ON b.customer_id = c.id WHERE b.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($customer_id, $guest_name, $phone, $room_id, $room_name, $check_in, $check_out, $guests, $total_price, $status, $payment_status, $note) {
        $stmt = $this->db->prepare("INSERT INTO bookings (customer_id, guest_name, phone, room_id, room_name, check_in, check_out, guests, total_price, status, payment_status, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $customer_id ?: null, 
            $guest_name, 
            $phone, 
            $room_id, 
            $room_name, 
            $check_in, 
            $check_out, 
            $guests, 
            $total_price, 
            $status, 
            $payment_status, 
            $note
        ]);
    }

    public function update($id, $customer_id, $guest_name, $phone, $room_id, $room_name, $check_in, $check_out, $guests, $total_price, $status, $payment_status, $note) {
        $stmt = $this->db->prepare("UPDATE bookings SET customer_id=?, guest_name=?, phone=?, room_id=?, room_name=?, check_in=?, check_out=?, guests=?, total_price=?, status=?, payment_status=?, note=? WHERE id=?");
        return $stmt->execute([
            $customer_id ?: null, 
            $guest_name, 
            $phone, 
            $room_id, 
            $room_name, 
            $check_in, 
            $check_out, 
            $guests, 
            $total_price, 
            $status, 
            $payment_status, 
            $note, 
            $id
        ]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE bookings SET status=? WHERE id=?");
        return $stmt->execute([$status, $id]);
    }

    public function updatePaymentStatus($id, $payment_status) {
        $stmt = $this->db->prepare("UPDATE bookings SET payment_status=? WHERE id=?");
        return $stmt->execute([$payment_status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteMultiple($ids) {
        if (empty($ids)) return false;
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    public function getActiveBookingsForYear($year) {
        $stmt = $this->db->prepare("
            SELECT id, check_in, check_out, total_price, status 
            FROM bookings 
            WHERE status != 'cancelled' 
              AND (YEAR(check_in) = ? OR YEAR(check_out) = ?)
        ");
        $stmt->execute([$year, $year]);
        return $stmt->fetchAll();
    }
}
