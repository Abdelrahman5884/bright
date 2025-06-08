<?php 
require "header.php";
require "config.php";

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $msg = $_POST['msg'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("INSERT INTO contact_us (contact_name, message, number, email, user_id) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$name, $msg, $number, $email, $user_id])) {
            $message[] = 'Message sent successfully!';
        } else {
            $message[] = 'Failed to send message!';
        }
    } catch(PDOException $e) {
        $message[] = 'Error: ' . $e->getMessage();
    }
}
?>
<section class="contact">

   <div class="row">

      <div class="image">
         <img src="images/contact-img.svg" alt="">
      </div>

      <form action="" method="post">
         <h3>Get in Touch</h3>
         <?php
         if(isset($message)) {
            foreach($message as $message) {
               echo '<div class="message">'.$message.'</div>';
            }
         }
         ?>
         <input type="text" placeholder="Enter your name" name="name" required maxlength="50" class="box">
         <input type="email" placeholder="Enter your email" name="email" required maxlength="50" class="box">
         <input type="number" placeholder="Enter your number" name="number" required maxlength="15" class="box">
         <textarea name="msg" class="box" placeholder="Enter your message" required maxlength="1000" cols="30" rows="10"></textarea>
         <input type="submit" value="Send Message" class="inline-btn" name="submit">
      </form>

   </div>

   <div class="box-container">

      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>Phone Number</h3>
         <a href="tel:01016789226">01016789226</a>
         <a href="tel:01004732940">01004732940</a>
      </div>
      
      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>Email Address</h3>
         <a href="bodyh0672@gmail.com">bodyh0672@gmail.com</a>
         <a href="mailto:educa@gmail.com">educa@gmail.com</a>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>Office Address</h3>
         <a href="#"> <br>  Mansoura</a>
      </div>

   </div>

</section>
<?php require "footer.php";  ?>