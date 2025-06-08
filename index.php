<?php 
require "header.php";
require "config.php";

$conn = Database::getInstance()->getConnection();

$stmt = $conn->prepare("SELECT * FROM categore");
$stmt->execute();
$categories = $stmt->fetchAll();

$stmt = $conn->prepare("
    SELECT c.*, u.name as tutor_name, u.profile_image as tutor_image, cat.categore_name 
    FROM courses c 
    JOIN users u ON c.user_id = u.id 
    LEFT JOIN categore cat ON c.categore_id = cat.categore_id 
    ORDER BY c.course_id DESC 
    LIMIT 2
");
$stmt->execute();
$featured_courses = $stmt->fetchAll();
?>

<section class="home-grid">
   <h1 class="heading">Quick Options</h1>

   <div class="box-container">
      <div class="box">
         <h3 class="title">Likes and Comments</h3>
         <p class="likes">Total Likes: <span>25</span></p>
         <a href="#" class="inline-btn">View Likes</a>
         <p class="likes">Total Comments: <span>12</span></p>
         <a href="#" class="inline-btn">View Comments</a>
         <p class="likes">Saved Playlists: <span>4</span></p>
         <a href="#" class="inline-btn">View Playlists</a>
      </div>

      <div class="box">
         <h3 class="title">Top Categories</h3>
         <div class="flex">
            <?php foreach($categories as $category): ?>
            <a href="courses.php?category=<?= $category['categore_id'] ?>">
               <i class="fas fa-code"></i>
               <span><?= htmlspecialchars($category['categore_name']) ?></span>
            </a>
            <?php endforeach; ?>
         </div>
      </div>

      <!-- <div class="box">
         <h3 class="title">Become a Tutor</h3>
         <p class="tutor">You can became a teacher by registration.<br> For registeration click the link below.</p>
         <a href="teachers.html" class="inline-btn">Get Started</a>
      </div> -->
   </div>
</section>

<section class="courses">
   <h1 class="heading">Our Courses</h1>

   <div class="box-container">
      <?php foreach($featured_courses as $course): ?>
      <div class="box">
         <div class="tutor">
            <img src="<?= htmlspecialchars($course['tutor_image'] ?? 'images/default-avatar.jpg') ?>" alt="Tutor Picture">
            <div class="info">
               <h3><?= htmlspecialchars($course['tutor_name']) ?></h3>
            </div>
         </div>
         <div class="thumb">
            <img src="<?= htmlspecialchars($course['image_url'] ?? 'images/default-course.jpg') ?>" alt="Course Thumbnail">
         </div>
         <h3 class="title"><?= htmlspecialchars($course['course_name']) ?></h3>
         <p class="category"><?= htmlspecialchars($course['categore_name'] ?? 'Uncategorized') ?></p>
         <p class="price">$<?= number_format($course['price'], 2) ?></p>
         <a href="playlist.php?course_id=<?= $course['course_id'] ?>" class="inline-btn">View Playlist</a>
      </div>
      <?php endforeach; ?>
   </div>

   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn">View All Courses</a>
   </div>
</section>

<?php require "footer.php";  ?>