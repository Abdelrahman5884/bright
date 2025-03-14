<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="icon" href="images/favicon.ico">

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<header class="header">
   <section class="flex">
      <a href="index.html" class="logo">Bright</a>

      <form action="search.html" method="post" class="search-form">
         <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
         <button type="submit" class="fas fa-search"></button>
      </form>
      
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <img src="images/pic-2.jpg" class="image" alt="">
         <h3 class="name">Myrna Nader</h3>
         <!-- <p class="role">student</p> -->
         <a href="profile.html" class="btn">view profile</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div>
     
      <div class="profile">
         <img src="images/pic-1.jpg" class="image" alt="Profile Picture">
         <h3 class="name">Shaikh Anas</h3>
         <!-- <p class="role">Student</p> -->
         <a href="profile.html" class="btn">View Profile</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      </div>
   </section>
</header>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>
   
   <div class="profile">
      <img src="images/pic-2.jpg" class="image" alt="">
      <h3 class="name">Myrna Nader</h3>
      <p class="role">Student</p>
      <a href="profile.html" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="index.html"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="about.html"><i class="fas fa-question"></i><span>About</span></a>
      <a href="courses.html"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="teachers.html"><i class="fas fa-chalkboard-user"></i><span>upload courese</span></a>
      <a href="contact.html"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>
<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        header("Location: payment.php");
        exit;
    } else {
        echo "<script>alert('Invalid login credentials!');</script>";
    }
}
?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Login Now</h3>
      <p>Your Email <span>*</span></p>
      <input type="email" name="email" placeholder="Enter your email" required maxlength="50" class="box">
      <p>Your Password <span>*</span></p>
      <input type="password" name="password" placeholder="Enter your password" required maxlength="20" class="box">
      <p>Don't have an account? <a href="register.php">Register</a></p>
      <input type="submit" value="Login" name="submit" class="btn">
   </form>
   </form>
</section>

<!-- Custom JS file link -->
<script src="js/script.js"></script>

</body>
</html>
