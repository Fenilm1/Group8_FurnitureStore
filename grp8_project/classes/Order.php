<?php
class Order
{
    private $conn;
    private $table_name = "orders";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create order
    function createOrder($userId, $total, $address)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, total, created_at) VALUES (:user_id, :total, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':total', $total);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Add order item
    function addOrderItem($orderId, $productId, $quantity, $price)
    {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);

        return $stmt->execute();
    }

    // Clear cart after order is placed
    function clearCart($userId)
    {
        $query = "DELETE FROM cart WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    // Get order by ID
    function getOrderById($orderId)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get order items by order ID
    function getOrderItems($orderId)
    {
        $query = "SELECT * FROM order_items WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
