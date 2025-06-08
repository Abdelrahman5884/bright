<?php 
require "header.php";
require "config.php";

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $c_pass = $_POST['c_pass'];
    $user_id = $_SESSION['user_id'];

    try {
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!password_verify($old_pass, $user['password'])) {
            $message[] = 'Old password is incorrect!';
        } else {
            if($new_pass != $c_pass) {
                $message[] = 'New passwords do not match!';
            } else {
                $update_data = [];
                $params = [];

                if(!empty($name)) {
                    $update_data[] = "name = ?";
                    $params[] = $name;
                }
                if(!empty($email)) {
                    $update_data[] = "email = ?";
                    $params[] = $email;
                }
                if(!empty($new_pass)) {
                    $update_data[] = "password = ?";
                    $params[] = password_hash($new_pass, PASSWORD_DEFAULT);
                }

                if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $image_name = $_FILES['image']['name'];
                    $image_tmp = $_FILES['image']['tmp_name'];
                    $image_path = basename($image_name);
                    move_uploaded_file($image_tmp, $image_path);

                    $update_data[] = "profile_image = ?";
                    $params[] = $image_path;
                }

                if(!empty($update_data)) {
                    $params[] = $user_id;
                    $sql = "UPDATE users SET " . implode(", ", $update_data) . " WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    $message[] = 'Profile updated successfully!';
                }
            }
        }
    } catch(PDOException $e) {
        $message[] = 'Error: ' . $e->getMessage();
    }
}
?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Update Profile</h3>
      <?php
      if(isset($message)) {
         foreach($message as $message) {
            echo '<div class="message">'.$message.'</div>';
         }
      }
      ?>
      <p>Update Name</p>
      <input type="text" name="name" placeholder="Enter your name" maxlength="50" class="box">
      <p>Update Email</p>
      <input type="email" name="email" placeholder="Enter your email" maxlength="50" class="box">
      <p>Old Password</p>
      <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box">
      <p>New Password</p>
      <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="20" class="box">
      <p>Confirm Password</p>
      <input type="password" name="c_pass" placeholder="Confirm your new password" maxlength="20" class="box">
      <p>Update Profile Picture</p>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" value="Update Profile" name="submit" class="btn">
   </form>
</section>

<?php require "footer.php"; ?>
