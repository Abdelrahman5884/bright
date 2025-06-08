<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login to comment']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? 0;
$comment = $data['comment'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($comment)) {
    echo json_encode(['error' => 'Comment cannot be empty']);
    exit;
}

try {
    $conn = Database::getInstance()->getConnection();

    $stmt = $conn->prepare("INSERT INTO comments (course_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$course_id, $user_id, $comment]);
        $stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    echo json_encode([
        'comment' => $comment,
        'user_name' => $user['name'],
        'user_image' => $user['profile_image']
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error occurred']);
} 