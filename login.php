<?php
require "header.php";
// require 'config.php';
require "singleton.php";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   $email = $_POST["email"];
   $password = $_POST["password"];
   Login::getInstance()->checkCredentials($email, $password);
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
<?php require "footer.php";  ?>
