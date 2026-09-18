<?php
namespace Models;

use Config\Database;
use PDO;

class Room {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($search = '', $status = '') {
        $sql = "SELECT * FROM rooms WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (room_name LIKE ? OR room_type LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $type, $capacity, $price, $status, $amenities) {
        $stmt = $this->db->prepare("INSERT INTO rooms (room_name, room_type, capacity, price, status, amenities) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $type, $capacity, $price, $status, $amenities]);
    }

    public function update($id, $name, $type, $capacity, $price, $status, $amenities) {
        $stmt = $this->db->prepare("UPDATE rooms SET room_name=?, room_type=?, capacity=?, price=?, status=?, amenities=? WHERE id=?");
        return $stmt->execute([$name, $type, $capacity, $price, $status, $amenities, $id]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE rooms SET status=? WHERE id=?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount($status = '') {
        if ($status !== '') {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM rooms WHERE status = ?");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM rooms");
        }
        return $stmt->fetchColumn();
    }
}
