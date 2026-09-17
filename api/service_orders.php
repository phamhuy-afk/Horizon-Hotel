<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $room_id = $_GET['room_id'] ?? '';
        $sql = "SELECT so.*, s.service_name, s.category FROM service_orders so JOIN services s ON so.service_id = s.id";
        $params = [];

        if ($room_id !== '') {
            $sql .= " WHERE so.room_id = ?";
            $params[] = $room_id;
        }

        $sql .= " ORDER BY so.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $orders]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->room_id) && !empty($data->service_id)) {
            $stmtService = $pdo->prepare("SELECT price FROM services WHERE id = ?");
            $stmtService->execute([$data->service_id]);
            $service = $stmtService->fetch();

            if (!$service) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Service not found."]);
                exit;
            }

            $quantity = $data->quantity ?? 1;
            $total_price = $quantity * $service['price'];
            $status = $data->status ?? 'pending';
            $note = $data->note ?? null;

            $stmt = $pdo->prepare("INSERT INTO service_orders (room_id, service_id, quantity, total_price, status, note) VALUES (?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$data->room_id, $data->service_id, $quantity, $total_price, $status, $note])) {
                echo json_encode(["status" => "success", "message" => "Service order created.", "id" => $pdo->lastInsertId()]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create service order."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Room ID and Service ID are required."]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->status)) {
            $stmt = $pdo->prepare("UPDATE service_orders SET status = ? WHERE id = ?");
            if ($stmt->execute([$data->status, $data->id])) {
                echo json_encode(["status" => "success", "message" => "Order status updated."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update order status."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Order ID and status are required."]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? (json_decode(file_get_contents("php://input"))->id ?? null);
        if (!empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM service_orders WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(["status" => "success", "message" => "Order deleted."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to delete order."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing order ID."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
