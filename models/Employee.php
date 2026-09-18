<?php
namespace Models;

use Config\Database;
use PDO;

class Employee {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT id, fullname, email, role, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, fullname, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($fullname, $email, $password_hash, $role) {
        $stmt = $this->db->prepare("INSERT INTO users (fullname, email, password_hash, role) VALUES (?,?,?,?)");
        return $stmt->execute([$fullname, $email, $password_hash, $role]);
    }

    public function update($id, $fullname, $email, $role, $password_hash = null) {
        if ($password_hash !== null) {
            $stmt = $this->db->prepare("UPDATE users SET fullname=?, email=?, password_hash=?, role=? WHERE id=?");
            return $stmt->execute([$fullname, $email, $password_hash, $role, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET fullname=?, email=?, role=? WHERE id=?");
            return $stmt->execute([$fullname, $email, $role, $id]);
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
}
