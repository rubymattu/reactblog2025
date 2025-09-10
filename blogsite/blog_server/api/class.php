<?php

ini_set('display_errors', 1);

error_reporting(E_ALL);

session_start();

 

header("Access-Control-Allow-Origin: http://localhost:3000");  // Or your frontend domain

header("Access-Control-Allow-Credentials: true");

header("Access-Control-Allow-Methods: POST, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type");

header("Content-Type: application/json");

// Handle preflight OPTIONS request

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(200);

    exit();

}

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

require_once('../config/config.php');

require_once('../config/database.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['userName'], $data['password'], $data['emailAddress'], $data['role'])) {

    echo json_encode(["success" => false, "message" => "Missing fields"]);

    exit;

}

$userName = mysqli_real_escape_string($conn, $data['userName']);

$emailAddress = mysqli_real_escape_string($conn, $data['emailAddress']);

$passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

$role = mysqli_real_escape_string($conn, $data['role']);

 

// Load admin secret from environment

$adminSecret = $_ENV['ADMIN_SECRET'] ?? null;

 

// If role is admin, check for secret key

if ($role === "admin") {

    if (!isset($data['secretKey']) || $data['secretKey'] !== $adminSecret) {

        echo json_encode( ["success" => false,
    "message" => "Debug info",
    "adminSecret" => $adminSecret,
    "secretKeyFromUser" => $data['secretKey'] ?? null]);

        exit;

    }

}

 

// Check if username or email already exists

$check = $conn->prepare("SELECT registrationID FROM registrations WHERE userName = ? OR emailAddress = ?");

$check->bind_param("ss", $userName, $emailAddress);

$check->execute();

$check->store_result();

 

if ($check->num_rows > 0) {

    echo json_encode(["success" => false, "message" => "Username or email already taken"]);

    exit;

}

$check->close();

 

// Insert new user with role

$stmt = $conn->prepare("INSERT INTO registrations (userName, password, emailAddress, role) VALUES (?, ?, ?, ?)");

$stmt->bind_param("ssss", $userName, $passwordHash, $emailAddress, $role);

 

if ($stmt->execute()) {

    echo json_encode(["success" => true, "message" => "Registration successful"]);

} else {

    echo json_encode(["success" => false, "message" => "Registration failed"]);

}

 

$stmt->close();

$conn->close();

?>


