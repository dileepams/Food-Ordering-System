<?php
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private $conn;
    private $user_id = 1;
    private $product_id = 1;
    private $client_ip = '127.0.0.1';

    protected function setUp(): void
    {
        $this->conn = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_NAME']);
        $this->conn->begin_transaction();
    }

    public function testAddToCart()
    {
        $qty = 1;

        // Add to cart
        $stmt = $this->conn->prepare("INSERT INTO cart (client_ip, user_id, product_id, qty) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siid", $this->client_ip, $this->user_id, $this->product_id, $qty);
        $stmt->execute();

        // Verify cart item
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $this->user_id, $this->product_id);
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
