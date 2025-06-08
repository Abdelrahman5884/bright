<?php 
require "header.php";
require "config.php";

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

try {
    $conn = Database::getInstance()->getConnection();
    
    echo "<!-- Debug: Course ID = " . $course_id . " -->";
    
    $stmt = $conn->prepare("
        SELECT c.*, u.name as teacher_name, u.profile_image, u.email
        FROM courses c
        INNER JOIN users u ON c.user_id = u.id
        WHERE c.course_id = :course_id
    ");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<!-- Debug: Course data = " . print_r($course, true) . " -->";

    if ($course) {
        $stmt = $conn->prepare("
            SELECT c.*, u.name as teacher_name, u.profile_image
            FROM courses c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $course['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $teacher_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="teacher-profile">
   <h1 class="heading">Teacher Profile</h1>
   <div class="details">
      <div class="tutor">
         <img src="<?= htmlspecialchars($course['profile_image'] ?? 'images/default-avatar.jpg') ?>" alt="Teacher Profile">
         <h3><?= htmlspecialchars($course['teacher_name'] ?? 'Teacher Name') ?></h3>
         <span>Teacher</span>
      </div>
      <div class="flex">
         <p><span>Email: </span><?= htmlspecialchars($course['email'] ?? 'Not provided') ?></p>
         <p><span>Phone: </span><?= htmlspecialchars($course['phone'] ?? 'Not provided') ?></p>
      </div>
   </div>
</section>

<section class="courses">
   <h1 class="heading">Teacher's Courses</h1>
   <div class="box-container">
      <?php foreach ($teacher_courses as $teacher_course): ?>
      <div class="box">
         <div class="thumb">
            <img src="<?= htmlspecialchars($teacher_course['image_url'] ?? 'images/default-course.jpg') ?>" alt="Course Thumbnail">
            <span><?= count(explode(',', $teacher_course['video_url'])) ?> videos</span>
         </div>
         <h3 class="title"><?= htmlspecialchars($teacher_course['course_name']) ?></h3>
         <p class="price">$<?= number_format($teacher_course['price'], 2) ?></p>
         <a href="playlist.php?course_id=<?= $teacher_course['course_id'] ?>" class="inline-btn">View Course</a>
      </div>
      <?php endforeach; ?>
   </div>
</section>

<?php 
    } else {
        echo '<div class="error">Course not found! Please make sure you are accessing this page with a valid course ID.</div>';
    }
} catch (PDOException $e) {
    echo '<div class="error">Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
require "footer.php"; 
?>
