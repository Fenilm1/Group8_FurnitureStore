<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/User.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Here you can process the order and generate the invoice
    header("Location: invoice.php");
    exit();
}
?>

<div class="container">
    <h2>Checkout</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="address">Shipping Address:</label>
            <textarea class="form-control" id="address" name="address" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Order</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>
