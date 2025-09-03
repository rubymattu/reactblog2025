<?php
  session_start();
  header("Content-Type: application/json");
  include "connect.php";

  $data = json_decode(file_get_contents("php://input"), true);

  if (!isset($data['userName'], $data['password'], $data['emailAddress'])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
  }

  $userName = mysqli_real_escape_string($con, $data['userName']);
  $emailAddress = mysqli_real_escape_string($con, $data['emailAddress']);
  $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

  // Check if username already exists
  $check = $con->prepare("SELECT registrationID FROM registrations WHERE userName = ?");
  $check->bind_param("s", $userName);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username already taken"]);
    exit;
  }
  $check->close();

  // Insert new user
  $stmt = $con->prepare("INSERT INTO registrations (userName, password, emailAddress) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $userName, $passwordHash, $emailAddress);

  if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful"]);
  } else {
    echo json_encode(["success" => false, "message" => "Registration failed"]);
  }
  $stmt->close();
  $con->close();
?>
