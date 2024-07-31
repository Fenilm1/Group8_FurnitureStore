<?php
session_start();
include_once '../includes/header.php';
include_once '../includes/nav.php';
include_once '../classes/Database.php';
include_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $product->id = $_GET['id'];
    $productData = $product->getProductById();

    if (!$productData) {
        $_SESSION['error'] = "Product not found.";
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product->id = $_POST['id'];
    $product->name = $_POST['name'];
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];
    $product->stock = $_POST['stock'];

    if ($product->update()) {
        $_SESSION['success'] = "Product updated successfully.";
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to update product.";
    }
}
?>

<div class="container">
    <h1>Edit Product</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $productData['id']; ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($productData['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($productData['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $productData['price']; ?>" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $productData['stock']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
