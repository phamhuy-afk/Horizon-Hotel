<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $action = $data->action ?? ($_GET['action'] ?? null);

    if ($action === 'login') {
        $email = $data->email ?? '';
        $password = $data->password ?? '';

        if (!empty($email) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                unset($user['password_hash']);
                echo json_encode(["status" => "success", "message" => "Login successful", "data" => $user]);
            }
            else {
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "Invalid credentials or user not found"]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        }
    }
    elseif ($action === 'register') {
        $fullname = $data->fullname ?? '';
        $email = $data->email ?? '';
        $password = $data->password ?? '';
        $role = $data->role ?? 'customer';

        if (!empty($fullname) && !empty($email) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                http_response_code(409);
                echo json_encode(["status" => "error", "message" => "Email already exists"]);
                exit;
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $password_hash, $role])) {
                echo json_encode(["status" => "success", "message" => "User registered successfully", "id" => $pdo->lastInsertId()]);
            }
            else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to register user"]);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Fullname, email, and password are required"]);
        }
    }
    else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid action. Use JSON format with 'action': 'login' or 'register'"]);
    }
}
else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed. Only POST is accepted."]);
}
?>
