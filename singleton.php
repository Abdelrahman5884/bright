<?php

require 'config.php';
// session_start();

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

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Invalid login credentials!');</script>";
                        return false;

        }
    }
}
?>
