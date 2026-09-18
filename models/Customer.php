<?php
namespace Models;

use Config\Database;
use PDO;

class Customer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM customers ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($fullname, $email, $phone, $address, $type) {
        $stmt = $this->db->prepare("INSERT INTO customers (fullname, email, phone, address, type) VALUES (?,?,?,?,?)");
        return $stmt->execute([$fullname, $email, $phone, $address, $type]);
    }

    public function update($id, $fullname, $email, $phone, $address, $type) {
        $stmt = $this->db->prepare("UPDATE customers SET fullname=?, email=?, phone=?, address=?, type=? WHERE id=?");
        return $stmt->execute([$fullname, $email, $phone, $address, $type, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function incrementBookings($id) {
        $stmt = $this->db->prepare("UPDATE customers SET total_bookings = total_bookings + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount() {
        return $this->db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    }
}
