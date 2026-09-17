<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $search = $_GET['search'] ?? '';
        $sql = "SELECT id, fullname, phone, email, address, total_bookings, type, created_at FROM customers";
        $params = [];
        
        if ($search !== '') {
            $sql .= " WHERE fullname LIKE ? OR phone LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["status" => "success", "data" => $customers]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->fullname) && !empty($data->phone)) {
            $stmt = $pdo->prepare("INSERT INTO customers (fullname, email, phone, address, type) VALUES (?, ?, ?, ?, ?)");
            $email = $data->email ?? null;
            $address = $data->address ?? null;
            $type = $data->type ?? 'normal';
            
            if ($stmt->execute([$data->fullname, $email, $data->phone, $address, $type])) {
                echo json_encode(["status" => "success", "message" => "Customer created.", "id" => $pdo->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create customer."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. (fullname, phone required)"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->fullname)) {
            $stmt = $pdo->prepare("UPDATE customers SET fullname=?, email=?, phone=?, address=?, type=? WHERE id=?");
            $email = $data->email ?? null;
            $phone = $data->phone ?? null;
            $address = $data->address ?? null;
            $type = $data->type ?? 'normal';
            
            if ($stmt->execute([$data->fullname, $email, $phone, $address, $type, $data->id])) {
                echo json_encode(["status" => "success", "message" => "Customer updated."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update customer."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. (id, fullname required)"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id ?? ($_GET['id'] ?? null);
        if (!empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(["status" => "success", "message" => "Customer deleted."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to delete customer."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing customer ID."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
