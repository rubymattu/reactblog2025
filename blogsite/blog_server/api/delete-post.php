<?php
  // Prevent any output before headers
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  // Handle CORS preflight request BEFORE any includes
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      header("Access-Control-Allow-Origin: http://localhost:3000");
      header("Access-Control-Allow-Methods: POST, OPTIONS, GET, DELETE");
      header("Access-Control-Allow-Headers: Content-Type");
      header("Access-Control-Allow-Credentials: true");
      http_response_code(200);
      exit();
  }

  // Set CORS headers for actual requests
  header("Access-Control-Allow-Origin: http://localhost:3000");
  header("Access-Control-Allow-Methods: POST, OPTIONS, GET, DELETE");
  header("Access-Control-Allow-Headers: Content-Type");
  header("Access-Control-Allow-Credentials: true");
  header("Content-Type: application/json");

  // Now safe to include dependencies
  require_once('../config/config.php');
  require_once('../config/database.php');
  require_once 'auth.php';


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data['id']) ? intval($data['id']) : 0;

      if ($id > 0) {
          $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
          $stmt->bind_param("i", $id);

          if ($stmt->execute()) {
              echo json_encode(["success" => true, "message" => "Post deleted successfully."]);
          } else {
              echo json_encode(["success" => false, "message" => "Failed to delete post."]);
          }

          $stmt->close();
      } else {
          echo json_encode(["success" => false, "message" => "Invalid post ID."]);
      }
  } else {
      echo json_encode(["success" => false, "message" => "Invalid request method."]);
  }

  $stmt->close();
  $conn->close();
?>
