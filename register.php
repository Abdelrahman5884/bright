<?php
require 'config.php';
require "header.php";

$conn = Database::getInstance()->getConnection();
if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $image_name = $_FILES["image"]["name"];
    $image_tmp = $_FILES["image"]["tmp_name"];
    $image_path = "images/" . basename($image_name);
    move_uploaded_file($image_tmp, $image_path);
   //  $image = $image_path;
    $stmt = $conn->prepare("INSERT INTO users (name, email,`password`, `profile_image`) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password, $image_path])) {
        echo "<script>alert('Registration successful Please login.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error registering user.');</script>";
    }
}
?>
<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <p>your name <span>*</span></p>
      <input type="text" name="name" placeholder="enter your name" required maxlength="50" class="box">
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" required maxlength="50" class="box">
      <p>your password <span>*</span></p>
      <input type="password" name="password" placeholder="enter your password" required maxlength="20" class="box">
      <p>confirm password <span>*</span></p>
      <input type="password" name="c_pass" placeholder="confirm your password" required maxlength="20" class="box">
      <p>select profile <span>*</span></p>
      <input type="file" name="image" accept="image/*" class="box">
      <p>Already have an account? <a href="login.php">Login</a></p>
      <input type="submit" value="register new" name="submit" class="btn">
   </form>
   
</section>


<?php require "footer.php";  ?>

