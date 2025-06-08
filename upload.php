<?php 

require "config.php";
// session_start();

$conn = Database::getInstance()->getConnection();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit();
}

// Get all categories
$stmt = $conn->prepare("SELECT * FROM categore");
$stmt->execute();
$categories = $stmt->fetchAll();

// معالجة رفع الملفات عند POST
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $course_name = $_POST["course_name"];
    $description = $_POST["course_describtion"];
    $price = $_POST["Course_Price"];
    $phone = $_POST["phone_number"];
    $category_id = $_POST["category"];
    $user_id = $_SESSION["user_id"];

    // Create upload directories if they don't exist
    if (!file_exists("images")) {
        mkdir("images", 0777, true);
    }
    if (!file_exists("videos courses")) {
        mkdir("videos courses", 0777, true);
    }

    // الصورة الرئيسية للكورس
    $image_name = $_FILES["Image_URL"]["name"];
    $image_tmp = $_FILES["Image_URL"]["tmp_name"];
    $image_path = "images/" . basename($image_name);
    move_uploaded_file($image_tmp, $image_path);

    // معالجة كل فيديو مرفوع
    $video_files = $_FILES["Video_URL"];

    for ($i = 0; $i < count($video_files["name"]); $i++) {
        $video_name = $video_files["name"][$i];
        $video_tmp = $video_files["tmp_name"][$i];
        $video_path = "videos courses/" . basename($video_name);

        // رفع الفيديو على السيرفر
        move_uploaded_file($video_tmp, $video_path);

        // إدخال بيانات الكورس في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO courses (course_name, image_url, video_url, describtion, price, phone, user_id, categore_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $course_name,
            $image_path,
            $video_path,
            $description,
            $price,
            $phone,
            $user_id,
            $category_id
        ]);
    }

    echo "<script>alert('✅ Courses uploaded successfully.'); window.location.href='courses.php';</script>";
    exit();
}
?>

<section class="form-container">
   <form action="upload.php" method="post" enctype="multipart/form-data">
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
