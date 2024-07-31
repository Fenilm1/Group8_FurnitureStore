<?php
session_start();
include_once '../includes/header.php';
include_once '../includes/nav.php';
include_once '../classes/Database.php';
include_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$products = $product->getAllProducts();
?>

<div class="container">
    <h1>Admin - Products</h1>
    <a href="add_product.php" class="btn btn-success mb-3">Add Product</a>

    <?php if ($products && count($products) > 0): ?>
        <div class="row">
            <?php foreach ($products as $row): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-text"><?php echo '$' . number_format($row['price'], 2); ?></p>
                            <p class="card-text">Stock: <?php echo $row['stock']; ?></p>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
