<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $status = $_GET['status'] ?? '';
        $sql = "SELECT * FROM bookings";
        $params = [];

        if ($status !== '') {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $bookings]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->guest_name) && !empty($data->room_id) && !empty($data->check_in) && !empty($data->check_out)) {
            $stmtRoom = $pdo->prepare("SELECT room_name, price FROM rooms WHERE id = ?");
            $stmtRoom->execute([$data->room_id]);
            $room = $stmtRoom->fetch();

            if (!$room) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Room not found."]);
                exit;
            }

            $room_name = $room['room_name'];
            $guests = $data->guests ?? 1;

            // Tính số ngày để ra tổng tiền
            try {
                $checkInObj = new DateTime($data->check_in);
                $checkOutObj = new DateTime($data->check_out);
                $interval = $checkInObj->diff($checkOutObj);
                $nights = $interval->days > 0 ? $interval->days : 1;
                $total_price = $nights * $room['price'];
            }
            catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Invalid date format."]);
                exit;
            }

            $status = $data->status ?? 'pending';
            $note = $data->note ?? null;

            $stmt = $pdo->prepare("INSERT INTO bookings (guest_name, room_id, room_name, check_in, check_out, guests, total_price, status, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$data->guest_name, $data->room_id, $room_name, $data->check_in, $data->check_out, $guests, $total_price, $status, $note])) {
                echo json_encode(["status" => "success", "message" => "Booking created.", "id" => $pdo->lastInsertId()]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create booking."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. (guest_name, room_id, check_in, check_out required)"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->status)) {
            $stmt = $pdo->prepare("UPDATE bookings SET status=? WHERE id=?");
            if ($stmt->execute([$data->status, $data->id])) {
                echo json_encode(["status" => "success", "message" => "Booking status updated."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update booking."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data (need id and status)."]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id ?? ($_GET['id'] ?? null);
        if (!empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(["status" => "success", "message" => "Booking deleted."]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to delete booking."]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing booking ID."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
