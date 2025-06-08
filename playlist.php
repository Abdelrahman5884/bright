<?php 
require "header.php";
require "config.php";
// session_start();

$conn = Database::getInstance()->getConnection();
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

try {
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

    $stmt = $conn->prepare("
        SELECT 
            co.*,
            u.name AS user_name,
            u.profile_image AS user_image
        FROM comments co
        LEFT JOIN users u ON co.user_id = u.id
        WHERE co.course_id = ?
        ORDER BY co.comment_id DESC
    ");
    $stmt->execute([$course_id]);
    $comments = $stmt->fetchAll();

    $stmt = $conn->prepare("SELECT COUNT(like_id) AS likes_count FROM likes WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $likes = $stmt->fetch();
    $likes_count = $likes['likes_count'] ?? 0;

    $user_liked = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT like_id FROM likes WHERE course_id = ? AND user_id = ?");
        $stmt->execute([$course_id, $_SESSION['user_id']]);
        $user_liked = $stmt->fetch() ? true : false;
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
   <title><?= htmlspecialchars($course['course_name'] ?? 'Course Details') ?></title>
   <style>
      .action-buttons {
         display: flex;
         align-items: center;
         gap: 15px;
         margin-left: 35%;
         margin-top: 1rem;
      }

      .like-btn, .sub-btn, .download-btn {
         display: flex;
         align-items: center;
         justify-content: center;
         height: 50px;
         width: 150px;
         background-color: #8e44ad;
         font-size: 18px;
         cursor: pointer;
         color: #fff;
         border-radius: 25px;
         transition: background 0.3s ease;
         text-align: center;
         padding: 10px;
         gap: 10px;
      }

      .like-btn:hover, .sub-btn:hover, .download-btn:hover {
         background-color: #732d91;
      }

      .like-btn i, .sub-btn i, .download-btn i {
         font-size: 22px;
      }

      .liked {
         background-color: #e74c3c !important;
      }

      .comments-section {
         width: 80%;
         margin: auto;
         margin-top: 2rem;
         padding: 20px;
         border-radius: 10px;
         background-color: #f9f9f9;
      }

      .comment-box {
         display: flex;
         align-items: center;
         gap: 10px;
         margin-bottom: 20px;
      }

      .comment-input {
         flex: 1;
         height: 40px;
         padding: 10px;
         font-size: 16px;
         border-radius: 20px;
         border: 1px solid #ccc;
      }

      .comment-submit {
         height: 40px;
         width: 100px;
         background-color: #8e44ad;
         color: white;
         border-radius: 20px;
         font-size: 16px;
         cursor: pointer;
         border: none;
      }

      .comment-submit:hover {
         background-color: #732d91;
      }

      .comment-list {
         margin-top: 1rem;
      }

      .comment-item {
         padding: 15px;
         border-bottom: 1px solid #ddd;
         display: flex;
         gap: 15px;
         align-items: start;
      }

      .comment-avatar {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         object-fit: cover;
      }

      .comment-content {
         flex: 1;
      }

      .comment-header {
         display: flex;
         align-items: center;
         gap: 10px;
         margin-bottom: 5px;
      }

      .comment-header h4 {
         margin: 0;
         color: #333;
      }

      .comment-text {
         color: #666;
         margin: 5px 0;
      }

      .comment-date {
         font-size: 12px;
         color: #999;
      }
   </style>
</head>
<body>
   
<?php if (!empty($course)): ?>
<section class="playlist-details">
   <h1 class="heading">Playlist Details</h1>

   <div class="row">
      <div class="column">
         <div class="thumb">
            <img src="<?= $course['image_url'] ?>" alt="Playlist Thumbnail">
         </div>
      </div>
      <div class="column">
         <div class="tutor">
            <img src="<?= $course['tutor_image'] ?? 'images/default-avatar.jpg' ?>" alt="Tutor Picture">
            <div>
               <h3><?= htmlspecialchars($course['tutor_name'] ?? 'Tutor') ?></h3>
            </div>
         </div>
   
         <div class="details">
            <h3>Course: <?= htmlspecialchars($course['course_name']) ?></h3>
            <h3>Price: $<?= number_format($course['price'], 2) ?></h3>
            <a href="teacher_profile.php?course_id=<?= $course_id ?>" class="inline-btn">View Profile</a>
         </div>
      </div>
   </div>
</section>

<section class="playlist-videos">
   <h1 class="heading">Playlist Videos</h1>
   <div class="box-container">
      <a class="box" href="watch-video.php?course_id=<?= $course_id ?>">
         <img src="<?= $course['image_url'] ?>" alt="Video Thumbnail">
         <h3><?= htmlspecialchars($course['describtion']) ?></h3>
      </a>
   </div>
</section>

<div class="action-buttons">
   <div class="like-btn <?= $user_liked ? 'liked' : '' ?>" onclick="toggleLike()">
      <i class="fas fa-thumbs-up"></i>
      <span id="like-count"><?= $likes_count ?></span>
   </div>

   <div class="sub-btn">
      <i class="fas fa-bell"></i>
      <a style="color:#ffffff;" href="payment.php?course_id=<?= $course_id ?>">Subscribe</a>
   </div>

   <div class="download-btn">
      <i class="fas fa-download"></i>
      <a style="color:#ffffff;" href="download_video.php?course_id=<?= $course_id ?>">Download Video</a>
   </div>
</div>

<div class="comments-section">
   <h3>Comments (<?= count($comments) ?>)</h3>
   <?php if(isset($_SESSION['user_id'])): ?>
   <div class="comment-box">
      <input class="comment-input" type="text" id="comment-text" placeholder="Add a comment..." required>
      <button class="comment-submit" onclick="addComment()">Comment</button>
   </div>
   <?php else: ?>
   <p>Please <a href="login.php">login</a> to comment</p>
   <?php endif; ?>
   
   <div id="comment-list" class="comment-list">
      <?php foreach ($comments as $comment): ?>
      <div class="comment-item">
         <img src="<?= htmlspecialchars($comment['user_image'] ?? 'images/default-avatar.jpg') ?>" 
              alt="User Avatar" 
              class="comment-avatar">
         <div class="comment-content">
            <div class="comment-header">
               <h4><?= htmlspecialchars($comment['user_name']) ?></h4>
            </div>
            <p class="comment-text"><?= htmlspecialchars($comment['comment']) ?></p>
         </div>
      </div>
      <?php endforeach; ?>
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
            action: document.querySelector('.like-btn').classList.contains('liked') ? 'unlike' : 'like'
         })
      });
      
      const data = await response.json();
      
      if(data.error) {
         alert(data.error);
         return;
      }
      
      document.getElementById('like-count').textContent = data.likes;
      document.querySelector('.like-btn').classList.toggle('liked');
   } catch (error) {
      console.error('Error:', error);
   }
}

async function addComment() {
   <?php if(!isset($_SESSION['user_id'])): ?>
   alert('Please login to comment');
   return;
   <?php endif; ?>

   const commentText = document.getElementById('comment-text').value;
   if (commentText.trim() === "") return;

   try {
      const response = await fetch('comment.php', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
         },
         body: JSON.stringify({
            course_id: <?= $course_id ?>,
            comment: commentText
         })
      });
      
      const data = await response.json();
      
      if(data.error) {
         alert(data.error);
         return;
      }
      
      const commentList = document.getElementById('comment-list');
      const commentItem = document.createElement('div');
      commentItem.classList.add('comment-item');
      commentItem.innerHTML = `
         <img src="${data.user_image || 'images/default-avatar.jpg'}" alt="User Avatar" class="comment-avatar">
         <div class="comment-content">
            <div class="comment-header">
               <h4>${data.user_name}</h4>
            </div>
            <p class="comment-text">${data.comment}</p>
         </div>
      `;
      
      commentList.insertBefore(commentItem, commentList.firstChild);
      document.getElementById('comment-text').value = '';
   } catch (error) {
      console.error('Error:', error);
   }
}
</script>
<?php endif; ?>
<?php require "footer.php"; ?>



