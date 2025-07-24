<?php

use PHPUnit\Framework\TestCase;

// require_once __DIR__ . '/../../singleton.php'; 
require_once __DIR__ . '/../../unittest.php'; 


class LoginTest extends TestCase {
    
    public function testInvalidCredentials() {
        $login = Login::getInstance();
        $result = $login->checkCredentials("invalid@example.com", "wrongpass122");

        $this->assertFalse($result);
    }

    
    public function testValidCredentials() {
        $login = Login::getInstance();
        $result = $login->checkCredentials("bodyh0672020@gmail.com", "123456");

        $this->assertTrue($result);
    }

    public function testEmptyCredentials() {
        $login = Login::getInstance();
        $result = $login->checkCredentials("", "");

        $this->assertFalse($result);
    }
    
}
?>