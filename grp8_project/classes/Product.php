<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $image_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new product
    function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, stock, image_url) VALUES (:name, :description, :price, :stock, :image_url)";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':image_url', $this->image_url);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Read all products
    function getAllProducts() {
        $query = "SELECT id, name, description, price, stock, image_url FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read one product by ID
    function getProductById($id) {
        $query = "SELECT id, name, description, price, stock, image_url FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a product
    function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description, price = :price, stock = :stock, image_url = :image_url WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':image_url', $this->image_url);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete a product
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
