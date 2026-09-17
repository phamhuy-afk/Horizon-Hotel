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
        $status = $_GET['status'] ?? '';

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

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $rooms]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->room_name) && !empty($data->room_type) && isset($data->price)) {
            $stmt = $pdo->prepare("INSERT INTO rooms (room_name, room_type, capacity, price, status, amenities) VALUES (?, ?, ?, ?, ?, ?)");
            $capacity = $data->capacity ?? 2;
            $status = $data->status ?? 'available';
            $amenities = isset($data->amenities) ? (is_string($data->amenities) ? $data->amenities : json_encode($data->amenities)) : '[]';

            if ($stmt->execute([$data->room_name, $data->room_type, $capacity, $data->price, $status, $amenities])) {
                echo json_encode(["status" => "success", "message" => "Room created.", "id" => $pdo->lastInsertId()]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create room."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. (room_name, room_type, price required)"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->room_name)) {
            $stmt = $pdo->prepare("UPDATE rooms SET room_name=?, room_type=?, capacity=?, price=?, status=?, amenities=? WHERE id=?");
            $capacity = $data->capacity ?? 2;
            $price = $data->price ?? 0;
            $status = $data->status ?? 'available';
            $amenities = isset($data->amenities) ? (is_string($data->amenities) ? $data->amenities : json_encode($data->amenities)) : '[]';

            if ($stmt->execute([$data->room_name, $data->room_type, $capacity, $price, $status, $amenities, $data->id])) {
                echo json_encode(["status" => "success", "message" => "Room updated."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update room."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. (id, room_name required)"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id ?? ($_GET['id'] ?? null);
        if (!empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(["status" => "success", "message" => "Room deleted."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to delete room."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing room ID."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
