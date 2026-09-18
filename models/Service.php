<?php
namespace Models;

use Config\Database;
use PDO;

class Service {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = '') {
        $sql = "SELECT * FROM services";
        $params = [];
        if ($status !== '') {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY category, service_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($service_name, $description, $price, $category, $status) {
        $stmt = $this->db->prepare("INSERT INTO services (service_name, description, price, category, status) VALUES (?,?,?,?,?)");
        return $stmt->execute([$service_name, $description, $price, $category, $status]);
    }

    public function update($id, $service_name, $description, $price, $category, $status) {
        $stmt = $this->db->prepare("UPDATE services SET service_name=?, description=?, price=?, category=?, status=? WHERE id=?");
        return $stmt->execute([$service_name, $description, $price, $category, $status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id=?");
        return $stmt->execute([$id]);
    }
}
