<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$products = $product->getAllProducts();
?>

<div class="container">
    <h1>Products</h1>
    <div class="row">
        <?php foreach ($products as $row): ?>
            <div class="col-md-4">
                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                <img src="admin/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 100px; height: 100px;">
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p><?php echo '$' . number_format($row['price'], 2); ?></p>
                <a href="cart.php?add=<?php echo $row['id']; ?>" class="btn btn-primary">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
