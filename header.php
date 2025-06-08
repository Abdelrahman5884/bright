<?php
session_start();
require "config.php";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
}


$current_page = basename($_SERVER['PHP_SELF']);
$guest_pages = ['login.php', 'register.php'];

$conn = Database::getInstance()->getConnection();
if (!isset($_SESSION['user_id']) && !in_array($current_page, $guest_pages)) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $userName = $user ? $user['name'] :"username";
    $userImage =  $user['profile_image'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="images/favicon.ico">
   <title>Bright</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<header class="header">
   
   <section class="flex">

      <a href="index.php" class="logo">Bright</a>

      <form action="#" method="post" class="search-form">
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
         <img src="<?php echo $userImage ?> " class="image" alt="">
         <h3 class="name"><?php echo @$userName ? @$userName :"username"?></h3>
         <!-- <p class="role">student</p> -->
         <a href="profile.php" class="btn">view profile</a>
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
      <img src="<?php echo $userImage ?>" class="image" alt="">
      <h3 class="name"><?php echo @$userName ? @$userName :"username"?></h3>
      <!-- <p class="role">student</p> -->
      <a href="profile.php" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="index.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>upload courese</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>

<div class="chat-icon">
    <a href="Chat.php"> <img src="images/happy.png" alt="ChatBot" width="70" height="70">
    </a>
  </div>
  
  <style>
  .chat-icon {
    position: fixed;
    bottom: 25px;
    right: 25px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    border-radius: 50%;
  }
  </style>