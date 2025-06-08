<?php 
require "header.php";
require "config.php";

$course_id = $_GET['course_id'] ?? 0;

$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

$stmt = $conn->prepare("SELECT * FROM videos WHERE course_id = ?");
$stmt->execute([$course_id]);
$videos = $stmt->fetchAll();
?>

<section class="playlist-details">
   <h1 class="heading">Course Details</h1>
   <div class="row">
      <div class="col">
         <img src="<?= $course['image_url'] ?? 'images/default-course.png' ?>" class="cover-img">
      </div>
      <div class="col">
         <h2><?= htmlspecialchars($course['course_name'] ?? 'Untitled Course') ?></h2>
         <div class="meta">
            <p>Category: <?= htmlspecialchars($course['category'] ?? 'General') ?></p>
            <p>Price: $<?= number_format($course['price'] ?? 0, 2) ?></p>
         </div>
         <p><?= htmlspecialchars($course['description'] ?? 'No description available') ?></p>
      </div>
   </div>
</section>

<section class="playlist-videos">
   <h2 class="heading">Course Videos</h2>
   <div class="video-grid">
      <?php foreach ($videos as $index => $video): ?>
      <div class="video-item">
         <div class="video-number"><?= $index + 1 ?></div>
         <video controls>
            <source src="<?= $video['video_url'] ?>" type="video/mp4">
         </video>
         <h3><?= htmlspecialchars($video['title'] ?? 'Untitled Video') ?></h3>
      </div>
      <?php endforeach; ?>
   </div>
</section>

<?php require "footer.php"; ?>