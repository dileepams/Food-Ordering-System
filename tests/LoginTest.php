<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_NAME']);
        $this->conn->begin_transaction();
    }

    public function testUserLogin()
    {
        $email = 'test@example.com';
        $password = 'password';
        $mobile = '1234567890';
        $address = '123 Test Street';
        $firstName = 'Test';
        $lastName = 'User';

        // Create a dummy user
        // SECURITY NOTE: The application currently uses md5() for password hashing.
        // This is a critical security vulnerability. md5() is cryptographically broken
        // and should be replaced with a modern, strong hashing algorithm like
        // password_hash() and password_verify().
        $hashed_password = md5($password);
        $stmt = $this->conn->prepare("INSERT INTO user_info (first_name, last_name, email, password, mobile, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashed_password, $mobile, $address);
        $stmt->execute();

        // Attempt to retrieve the user
        $stmt = $this->conn->prepare("SELECT * FROM user_info WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        $this->assertEquals(1, $result->num_rows);
    }

    protected function tearDown(): void
    {
        $this->conn->rollback();
        $this->conn->close();
    }
}
