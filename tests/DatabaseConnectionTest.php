<?php
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testConnection()
    {
        $conn = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_NAME']);
        $this->assertNull($conn->connect_error);
    }
}
