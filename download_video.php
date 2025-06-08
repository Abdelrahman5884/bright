<?php
require "config.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

try {
    $conn = Database::getInstance()->getConnection();
    
    // Get course details
    $stmt = $conn->prepare("
        SELECT c.*, u.id as user_id 
        FROM courses c 
        LEFT JOIN users u ON c.user_id = u.id 
        WHERE c.course_id = ?
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        die("Course not found");
    }

    // Check if user has purchased the course
    $stmt = $conn->prepare("
        SELECT * FROM payments 
        WHERE course_id = ? AND user_id = ? AND status = 'completed'
    ");
    $stmt->execute([$course_id, $_SESSION['user_id']]);
    $purchase = $stmt->fetch();

    // Allow download if user is the course creator or has purchased the course
    if ($course['user_id'] == $_SESSION['user_id'] || $purchase) {
        $video_path = $course['video_url'];
        
        if (file_exists($video_path)) {
            // Get file info
            $file_name = basename($video_path);
            $file_size = filesize($video_path);
            
            // Set headers for download
            header('Content-Type: video/mp4');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Length: ' . $file_size);
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output file
            readfile($video_path);
            exit;
        } else {
            die("Video file not found");
        }
    } else {
        die("You need to purchase this course to download the video");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?> 