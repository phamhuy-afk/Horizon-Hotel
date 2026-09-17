<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $category = $_GET['category'] ?? '';
        $sql = "SELECT * FROM services WHERE status = 'active'";
        $params = [];
        
        if ($category !== '') {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["status" => "success", "data" => $services]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->service_name)) {
            $stmt = $pdo->prepare("INSERT INTO services (service_name, description, price, category, status) VALUES (?, ?, ?, ?, ?)");
            $description = $data->description ?? null;
            $price = $data->price ?? 0;
            $category = $data->category ?? 'other';
            $status = $data->status ?? 'active';
            
            if ($stmt->execute([$data->service_name, $description, $price, $category, $status])) {
                echo json_encode(["status" => "success", "message" => "Service created.", "id" => $pdo->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create service."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Service name is required."]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->service_name)) {
            $stmt = $pdo->prepare("UPDATE services SET service_name=?, description=?, price=?, category=?, status=? WHERE id=?");
            $description = $data->description ?? null;
            $price = $data->price ?? 0;
            $category = $data->category ?? 'other';
            $status = $data->status ?? 'active';
            
            if ($stmt->execute([$data->service_name, $description, $price, $category, $status, $data->id])) {
                echo json_encode(["status" => "success", "message" => "Service updated."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update service."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID and service name are required."]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id ?? ($_GET['id'] ?? null);
        if (!empty($id)) {
            $stmt = $pdo->prepare("UPDATE services SET status = 'inactive' WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(["status" => "success", "message" => "Service deactivated."]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to deactivate service."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing service ID."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
