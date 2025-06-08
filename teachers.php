<?php 
require "config.php";
require "header.php";
// session_start();

if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit();
}

$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare("SELECT * FROM categore");
$stmt->execute();
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
   $name = $_POST["course_name"];
   $image_name = $_FILES["Image_URL"]["name"];
   $image_tmp = $_FILES["Image_URL"]["tmp_name"];
   $image_path = "images/" . basename($image_name);
   move_uploaded_file($image_tmp, $image_path);

   $Course_Price = $_POST["Course_Price"];
   $course_describtion = $_POST["course_describtion"];
   $phone_number = $_POST["phone_number"];
   $category_id = $_POST["category"];
   $user_id = $_SESSION["user_id"];

   $video_files = $_FILES["Video_URL"];

   for ($i = 0; $i < count($video_files["name"]); $i++) {
      $video_name = $video_files["name"][$i];
      $video_tmp = $video_files["tmp_name"][$i];
      $video_path = "videos courses/" . basename($video_name);
      move_uploaded_file($video_tmp, $video_path);

      $stmt = $conn->prepare("INSERT INTO courses (course_name, image_url, video_url, describtion, price, phone, user_id, categore_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([$name, $image_path, $video_path, $course_describtion, $Course_Price, $phone_number, $user_id, $category_id]);
   }

   echo "<script>alert('Courses uploaded successfully.'); window.location.href='courses.php';</script>";
}
?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Create Course</h3>
      <p>Course name <span>*</span></p>
      <input type="text" name="course_name" required class="box">
      <p>Image URL <span>*</span></p>
      <input type="file" accept="image/*" name="Image_URL" required class="box">
      <p>Video Files <span>*</span></p>
      <input type="file" name="Video_URL[]" accept="video/*" multiple required class="box">
      <p>Course Price <span>*</span></p>
      <input type="text" name="Course_Price" required class="box">
      <p>Course Description <span>*</span></p>
      <input type="text" name="course_describtion" required class="box">
      <p>Phone <span>*</span></p>
      <input type="text" name="phone_number" required class="box">
      <p>Category <span>*</span></p>
      <select name="category" required class="box">
         <?php foreach($categories as $category): ?>
            <option value="<?= $category['categore_id'] ?>"><?= htmlspecialchars($category['categore_name']) ?></option>
         <?php endforeach; ?>
      </select>
      <input type="submit" value="Upload" class="btn">
   </form>
</section>

<?php require "footer.php"; ?>

