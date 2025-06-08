<?php

require_once 'config.php';

class Login {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Login();
        }
        return self::$instance;
    }

    public function checkCredentials($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && password_verify($password, $user['password']);
    }

    public function getUser($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php 
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   $email = $_POST["email"];
   $password = $_POST["password"];
   Login::getInstance()->checkCredentials($email, $password);
}
?>