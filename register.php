<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="images/favicon.ico">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<header class="header">
   
   <section class="flex">

      <a href="index.html" class="logo">Bright</a>

      <form action="search.html" method="post" class="search-form">
         <input type="text" name="search_box" required placeholder="search courses..." maxlength="100">
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
         <!-- <p class="role">Student</p> -->
         <a href="profile.html" class="btn">view profile</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
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
      <!-- <p class="role">Student</p> -->
      <a href="profile.html" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="index.html"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.html"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.html"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.html"><i class="fas fa-chalkboard-user"></i><span>upload courese</span></a>
      <a href="contact.html"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>
<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    

    $stmt = $conn->prepare("INSERT INTO users (name, email,`password`) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $password])) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='login.php';</script>";
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
      <input type="file" accept="image/*" required class="box">
      <p>Already have an account? <a href="login.php">Login</a></p>
      <input type="submit" value="register new" name="submit" class="btn">
   </form>
   
</section>



<!-- custom js file link  -->
<script src="js/script.js"></script>

   
</body>
</html>