
<?php

if (!class_exists('Database')) {
  class Database {
      private static $instance = null;
      private $conn;
      private $host = "localhost";
      private $db = "bright";
      private $user = "root";
      private $pass = "";

      private function __construct() {
          $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db;charset=utf8", $this->user, $this->pass);
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }

      public static function getInstance() {
          if (self::$instance === null) {
              self::$instance = new Database();
          }
          return self::$instance;
      }

      public function getConnection() {
          return $this->conn;
      }
  }
}
?>
