<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
header("Access-Control-Allow-Origin: http://localhost:3000");  // Or specify your frontend domain
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../config/config.php');
require_once('../config/database.php');

require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { // handle preflight
    http_response_code(200);
    exit();
}

// Validate required fields from form-data
if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['author'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Error: Missing or empty required parameter']);
    exit();
}

// Sanitize input
$title   = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
$author  = filter_var($_POST['author'], FILTER_SANITIZE_STRING);

// Handle file upload (optional)
$imageName = null;
$uploadDir = __DIR__ . '/uploads/';

if (!empty($_FILES['image']['name'])) {
    $originalName = basename($_FILES['image']['name']);
    $targetFilePath = $uploadDir . $originalName;

    // Check if file already exists
    if (file_exists($targetFilePath)) {
        http_response_code(400);
        echo json_encode(['message' => 'File already exists: ' . $originalName]);
        exit();
    }

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        http_response_code(500);
        echo json_encode([
            'message' => 'Error uploading file',
            'php_error' => $_FILES['image']['error']
        ]);
        exit();
    }

    $imageName = $originalName;
} else {
    $imageName = 'placeholder.jpg';
}
// Prepare SQL with imageName column
$stmt = $conn->prepare('INSERT INTO blog_posts (title, content, author, imageName) VALUES(?, ?, ?, ?)');
$stmt->bind_param('ssss', $title, $content, $author, $imageName);

// Execute statement
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    http_response_code(201);
    echo json_encode([
        'message' => 'Post created successfully',
        'id' => $id,
        'imageName' => $imageName
    ]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Error creating post: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
