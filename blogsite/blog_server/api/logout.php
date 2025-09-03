<?php
  session_start();
  header("Access-Control-Allow-Origin: http://localhost:3000");  // Or specify your frontend domain
  header("Access-Control-Allow-Methods: POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
  header("Content-Type: application/json");


  session_unset();
  session_destroy();

  echo json_encode(["success" => true, "message" => "Logged out"]);
?>
