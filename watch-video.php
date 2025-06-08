<?php
// session_start();
require 'config.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$user_id = $_SESSION['user_id'];

try {
    $conn = Database::getInstance()->getConnection();
    
    $stmt = $conn->prepare("SELECT paid_full FROM payments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment || !$payment['paid_full']) {
        echo "<script>alert('Please complete the payment first!'); window.location.href='payment.php?course_id=" . $course_id . "';</script>";
        exit;
    }
    
    $stmt = $conn->prepare("
        SELECT 
            c.*,
            u.name AS tutor_name,
            u.profile_image AS tutor_image,
            cat.categore_name AS category_name
        FROM courses c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN categore cat ON c.categore_id = cat.categore_id
        WHERE c.course_id = ?
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        echo "<script>alert('Course not found!'); window.location.href='courses.php';</script>";
        exit;
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['course_name']) ?> - Watch Video</title>
    <style>
        .watch-video {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .video-container {
            width: 1000px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .video-player {
            width: 100%;
            height: 300px;
            background: #000;
        }
        
        .video-player video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .course-info {
            padding: 20px;
        }
        
        .course-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .tutor-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tutor-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .course-title {
            font-size: 24px;
            margin: 0;
            color: #333;
        }
        
        .course-description {
            color: #666;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border-radius: 25px;
            background: #8e44ad;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #732d91;
        }

        .action-btn i {
            font-size: 18px;
        }

        .action-btn.liked {
            background: #e74c3c;
        }

        .action-btn.liked:hover {
            background: #c0392b;
        }

        .action-btn.liked i {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="watch-video">
        <div class="video-container">
            <div class="video-player">
                <video controls autoplay>
                    <source src="<?= htmlspecialchars($course['video_url']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div class="course-info">
                <div class="course-header">
                    <div class="tutor-info">
                        <img src="<?= htmlspecialchars($course['tutor_image'] ?? 'images/default-avatar.jpg') ?>" 
                             alt="Tutor" 
                             class="tutor-avatar">
                        <div>
                            <h2><?= htmlspecialchars($course['tutor_name']) ?></h2>
                            <p><?= htmlspecialchars($course['category_name']) ?></p>
                        </div>
                    </div>
                </div>
                
                <h1 class="course-title">Course: <?= htmlspecialchars($course['course_name']) ?></h1>
                <h2 class="course-description">Description: <?= htmlspecialchars($course['describtion']) ?></h2>

                <div class="action-buttons">
                    <a href="playlist.php?course_id=<?= $course_id ?>" class="action-btn">
                        <i class="fas fa-list"></i>
                        <span>Back to Course</span>
                    </a>
                    <button class="action-btn" onclick="toggleLike()">
                        <i class="far fa-heart"></i>
                        <span>Like Course</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    async function toggleLike() {
        <?php if(!isset($_SESSION['user_id'])): ?>
        alert('Please login to like this course');
        return;
        <?php endif; ?>

        try {
            const response = await fetch('like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    course_id: <?= $course_id ?>,
                    action: document.querySelector('.action-btn').classList.contains('liked') ? 'unlike' : 'like'
                })
            });
            
            const data = await response.json();
            
            if(data.error) {
                alert(data.error);
                return;
            }
            
            const likeBtn = document.querySelector('.action-btn:last-child');
            likeBtn.classList.toggle('liked');
            const icon = likeBtn.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
        } catch (error) {
            console.error('Error:', error);
        }
    }
    </script>
</body>
</html>

<?php require 'footer.php'; ?>
