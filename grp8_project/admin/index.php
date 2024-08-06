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
<script>
    window.addEventListener('pageshow', function(event) {
        var historyTraversal = event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2);
        if (historyTraversal) {
            window.location.href = "../logout.php";
        }
    });
</script>
<div class="container mt-5">
    <h1 class="text-center mb-4">Admin - Products</h1>
    <a href="add_product.php" class="btn btn-success mb-3">Add Product</a>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" id="message">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" id="message">
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if ($products && count($products) > 0): ?>
        <div class="row">
            <?php foreach ($products as $row): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="../<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-text"><?php echo '$' . number_format($row['price'], 2); ?></p>
                            <p class="card-text">Stock: <?php echo htmlspecialchars($row['stock']); ?></p>
                            <div class="d-flex justify-content-between mt-3">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                            </div>
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
