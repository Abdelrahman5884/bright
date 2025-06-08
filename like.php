<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please login to like this course']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? 0;
$action = $data['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    $conn = Database::getInstance()->getConnection();
    
    if ($action === 'like') {
        $stmt = $conn->prepare("SELECT like_id FROM likes WHERE course_id = ? AND user_id = ?");
        $stmt->execute([$course_id, $user_id]);
        if (!$stmt->fetch()) {
            $stmt = $conn->prepare("INSERT INTO likes (course_id, user_id) VALUES (?, ?)");
            $stmt->execute([$course_id, $user_id]);
        }
    } else if ($action === 'unlike') {
        $stmt = $conn->prepare("DELETE FROM likes WHERE course_id = ? AND user_id = ?");
        $stmt->execute([$course_id, $user_id]);
    }

    $stmt = $conn->prepare("SELECT COUNT(like_id) AS likes_count FROM likes WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $likes = $stmt->fetch();
    
    echo json_encode(['likes' => $likes['likes_count']]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error occurred']);
} 