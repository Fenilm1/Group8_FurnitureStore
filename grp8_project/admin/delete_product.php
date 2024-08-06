<?php
session_start();
include_once '../includes/header.php';
include_once '../includes/nav.php';
include_once '../classes/Database.php';
include_once '../classes/Product.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    $database = new Database();
    $db = $database->getConnection();

    $product = new Product($db);
    $product->id = $productId;

    // Delete the product
    if ($product->delete()) {
        $_SESSION['success'] = "Product deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete product.";
    }
} else {
    $_SESSION['error'] = "Invalid product ID.";
}

header("Location: index.php");
exit();
?>
