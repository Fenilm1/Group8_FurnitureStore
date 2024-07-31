<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $email;
    public $is_admin;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, email=:email, is_admin=:is_admin";

        $stmt = $this->conn->prepare($query);

        $this->username = sanitizeInput($this->username);
        $this->password = sanitizeInput($this->password);
        $this->email = sanitizeInput($this->email);
        $this->is_admin = sanitizeInput($this->is_admin);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", password_hash($this->password, PASSWORD_BCRYPT));
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":is_admin", $this->is_admin);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login user
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->username);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->is_admin = $row['is_admin'];
                return true;
            }
        }
        return false;
    }
}
?>
