<?php

$host = "localhost";
$dbname = "bright1";
$username = "root"; 
$password = ""; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<?php 

$dsn ="mysql:host=localhost;dbname=bright;charset=utf8";
$user = "root";
$pass = "";

try {
     $pdo = new pdo($dsn,$user,$pass); 
    //   $p = "insert into `user` (`user_name`,`phone` , `user_ID`) value ('abdo',123,50)";
    //   $s = "select * from `user`";
      $pdo->setAttribute(Pdo::ATTR_ERRMODE,pdo::ERRMODE_EXCEPTION);
    //   $pdo->exec($p); 
        
 }
 catch(PDOException $e){
echo "failed " . $e->getMessage();
 }



?>
