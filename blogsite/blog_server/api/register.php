<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  session_start();
  header("Access-Control-Allow-Origin: http://localhost:3000");  // Or specify your frontend domain
  header("Access-Control-Allow-Methods: POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
  header("Content-Type: application/json");

  require_once('../config/config.php');
  require_once('../config/database.php');;

  $data = json_decode(file_get_contents("php://input"), true);

  if (!isset($data['userName'], $data['password'], $data['emailAddress'], $data['role'])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
  }

  $userName = mysqli_real_escape_string($conn, $data['userName']);
  $emailAddress = mysqli_real_escape_string($conn, $data['emailAddress']);
  $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
  $role = isset($data['role']) ? mysqli_real_escape_string($conn, $data['role']) : 'user';

  // Check if username already exists
  $check = $conn->prepare("SELECT registrationID FROM registrations WHERE userName = ? OR emailAddress = ?");
  $check->bind_param("ss", $userName, $emailAddress);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username or Email Already Exists"]);
    exit;
  }
  $check->close();

  // Insert new user
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
